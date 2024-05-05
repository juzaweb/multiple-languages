<?php

namespace Juzaweb\Multilang\Actions;

use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Facades\HookAction;
use Juzaweb\CMS\Models\Language;

class MultilangAction extends Action
{
    public function handle(): void
    {
        $this->addAction(Action::BACKEND_INIT, [$this, 'adminActions']);
        $this->addAction(Action::POSTS_FORM_RIGHT_ACTION, [$this, 'addSelectLangPost'], 5);
        $this->addAction(Action::INIT_ACTION, [$this, 'addConfigs']);
        //$this->addFilter('post_type.parseDataForSave', [$this, 'parseDataPostForSave']);
        //$this->addAction('post_types.after_save', [$this, 'parseDataPostForSave']);
        //$this->addFilter('post.selectFrontendBuilder', [$this, 'changeFrontendQueryBuilder']);
        //$this->addAction('frontend.post_type.posts.detail.post', [$this, 'showPostDetailFrontend']);
    }

    public function showPostDetailFrontend(Post $post): void
    {
        if (get_config('mlla_type') && $locale = app()->getLocale()) {
            abort_if($post->locale !== $locale, 404);
        }
    }

    public function changeFrontendQueryBuilder($builder)
    {
        if (get_config('mlla_type') && $locale = app()->getLocale()) {
            $builder->where('locale', $locale);
        }

        return $builder;
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
}
