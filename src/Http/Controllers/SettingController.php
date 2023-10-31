<?php

namespace Juzaweb\Multilang\Http\Controllers;

use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Juzaweb\Backend\Http\Controllers\Backend\PageController;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Http\Requests\SaveSettingRequest;

class SettingController extends PageController
{
    public function index(): Factory|View
    {
        $title = trans('cms::app.setting');
        $languages = Language::get();
        $subdomains = get_config('mlla_subdomain', []);

        return view(
            'mlla::setting',
            compact(
                'title',
                'languages',
                'subdomains'
            )
        );
    }

    public function save(SaveSettingRequest $request): JsonResponse
    {
        $type = $request->post('mlla_type');
        $subdomain = [];

        if ($type == 'subdomain') {
            $languages = Language::get();
            $langCodes = $languages->pluck('code')->toArray();

            $subdomain = $request->post('mlla_subdomain', []);
            $subdomain = collect($subdomain)
                ->unique('language')
                ->unique('sub')
                ->map(
                    function ($item) use ($request) {
                        $sub = Str::slug($item['sub']);
                        return [
                        'language' => $item['language'],
                        'sub' => $sub,
                        'domain' => $sub.'.'.str_replace('www.', '', $request->getHost()),
                        ];
                    }
                )
                ->filter(
                    function ($item) use ($langCodes) {
                        return !empty($item['sub'])
                        && in_array($item['language'], $langCodes);
                    }
                )
                ->keyBy('domain');

            $subdomain = $subdomain->values()->keyBy('domain');
        }

        DB::beginTransaction();
        try {
            set_config('mlla_type', $type);
            set_config('mlla_subdomain', $subdomain);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $this->success(
            [
                'message' => trans('cms::app.save_successfully')
            ]
        );
    }
}
