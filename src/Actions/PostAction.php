<?php

namespace Juzaweb\Multilang\Actions;

use Illuminate\Database\Eloquent\Collection;
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Models\PostTranslation;

class PostAction extends Action
{
    public function handle(): void
    {
        $this->addAction(Action::POSTS_FORM_RIGHT_ACTION, [$this, 'addSelectLangPost'], 5);
        $this->addFilter('post-type.get-attribute', [$this, 'getPostAttribute'], 20, 3);
        $this->addFilter('post_type.getDataForForm', [$this, 'getPostDataForForm']);
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

    public function getPostAttribute($value, Post $post, string $key)
    {
        if (!in_array($key, (new PostTranslation)->getFillable())) {
            return $value;
        }

        $locale = app()->getLocale();

        if (!isset($post->translations) || !($post->translations instanceof Collection)) {
            return $value;
        }

        return $post->translations->where('locale', $locale)->first()[$key] ?? $value;
    }

    public function getPostDataForForm($data)
    {
        $locale = request()?->get('locale', Language::default()?->code);

        $data['model']->fill(
            $data['model']->translations()->where('locale', $locale)->firstOrNew()->toArray()
        );

        return $data;
    }
}
