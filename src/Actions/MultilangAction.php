<?php

namespace Juzaweb\Multilang\Actions;

use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Models\PostTranslation;

class MultilangAction extends Action
{
    public function handle(): void
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'adminActions']);
        $this->addAction(Action::POSTS_FORM_RIGHT_ACTION, [$this, 'addSelectLangPost'], 5);
        $this->addAction(Action::INIT_ACTION, [$this, 'addConfigs']);
        $this->addFilter('post-type.get-attribute', [$this, 'getPostAttribute'], 20, 3);
        $this->addFilter('post_type.getDataForForm', [$this, 'getPostDataForForm']);
    }

    public function adminActions(): void
    {
        HookAction::registerAdminPage(
            'multilingual',
            [
                'title' => trans('cms::app.multilingual'),
                'menu' => [
                    'position' => 30,
                    'parent' => 'setting',
                ]
            ]
        );

        HookAction::registerAdminPage(
            'languages',
            [
                'title' => trans('cms::app.languages'),
                'menu' => [
                    'position' => 30,
                    'parent' => 'managements',
                ]
            ]
        );

        $this->enqueueScript(
            'mlla',
            plugin_asset('js/select-language.min.js', 'juzaweb/multiple-languages'),
            '1.0',
            true
        );
    }

    public function addSelectLangPost($model): void
    {
        $default = get_config('language', 'en');
        $selected = request()?->get('locale') ?? $default;
        $languages = Language::languages()->pluck('name', 'code');

        echo e(
            view(
                'mlla::select_lang',
                compact(
                    'model',
                    'languages',
                    'selected'
                )
            )
        );
    }

    public function addConfigs(): void
    {
        HookAction::registerConfig(['mlla_type', 'mlla_subdomain']);
    }

    public function getPostAttribute($value, Post $post, string $key)
    {
        if (!in_array($key, (new PostTranslation)->getFillable())) {
            return $value;
        }

        $locale = app()->getLocale();

        if ($locale == Language::default()?->code) {
            return $value;
        }

        return $post->translations->where('locale', $locale)->first()[$key] ?? $value;
    }

    public function getPostDataForForm($data)
    {
        $locale = request()?->get('locale', Language::default()?->code);

        if ($locale != Language::default()?->code) {
            $data['model']->fill(
                $data['model']->translations()->where('locale', $locale)->first()->toArray()
            );
        }

        return $data;
    }
}
