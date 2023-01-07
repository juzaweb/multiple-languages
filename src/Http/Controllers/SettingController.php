<?php

namespace Juzaweb\Multilang\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Juzaweb\Backend\Http\Controllers\Backend\PageController;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Http\Requests\SaveSettingRequest;
use Juzaweb\Network\Facades\Network;
use Juzaweb\Network\Models\DomainMapping;

class SettingController extends PageController
{
    public function index(): \Illuminate\Contracts\View\View
    {
        $title = trans('cms::app.setting');
        $languages = Language::get();
        $subdomains = get_config('mlla_subdomain', []);
        
        return view(
            'multilang::setting',
            compact(
                'title',
                'languages',
                'subdomains'
            )
        );
    }
    
    public function save(SaveSettingRequest $request): JsonResponse|RedirectResponse
    {
        $type = $request->post('mlla_type');
        $subdomain = [];
        $domains = [];

        if ($type == 'subdomain') {
            $languages = Language::get();
            $langCodes = $languages->pluck('code')->toArray();
            
            $subdomain = $request->post('mlla_subdomain', []);
            $subdomain = collect($subdomain)
                ->unique('language')
                ->unique('sub')
                ->map(
                    fn ($item) => [
                        'language' => $item['language'],
                        'sub' => Str::slug($item['sub']),
                        'domain' => Str::slug($item['sub']).'.'.$request->getHost(),
                    ]
                )
                ->filter(
                    fn($item) => !empty($item['sub']) && in_array($item['language'], $langCodes)
                )
                ->keyBy('domain');

            $domains = $subdomain->pluck('domain')->toArray();
            $subdomain = $subdomain->values();
        }
        
        DB::beginTransaction();
        try {
            set_config('mlla_type', $type);
            set_config('mlla_subdomain', $subdomain);

            if (config('network.enable')) {
                $siteId = Network::getCurrentSiteId();
                
                DomainMapping::where(
                    [
                        'plugin' => 'multilang',
                        'site_id' => $siteId,
                    ]
                )
                    ->whereNotIn('domain', $domains)
                    ->delete();

                if ($type == 'domain') {
                    foreach ($subdomain as $sub) {
                        DomainMapping::firstOrCreate(
                            [
                                'domain' => $sub['domain'],
                            ],
                            [
                                'plugin' => 'multilang',
                                'site_id' => $siteId,
                            ]
                        );
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
        
        return $this->success(trans('cms::app.save_successfully'));
    }
}
