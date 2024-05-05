<?php

namespace Juzaweb\Multilang\Actions;

use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Models\PostTranslation;

class FrontendAction extends Action
{
    public function handle(): void
    {
        $this->addFilter('post.withFrontendDefaults', [$this, 'addFrontendWithDefaults']);
        $this->addFilter('post.selectFrontendBuilder', [$this, 'changeFrontendQueryBuilder']);
        $this->addFilter('frontend.getPostBySlug', [$this, 'getPostBySlug'], 20, 2);
        $this->addAction('frontend.post_type.detail.post', [$this, 'showPostDetailFrontend']);
    }

    public function showPostDetailFrontend(Post $post): void
    {
        if (!is_home_page($post) && get_config('mlla_type') && ($locale = app()->getLocale())) {
            abort_if($locale != Language::default()->code && $post->translations->where('locale', $locale)->isEmpty(), 404);
        }
    }

    public function changeFrontendQueryBuilder($builder)
    {
        if (get_config('mlla_type') && ($locale = app()->getLocale()) && $locale != Language::default()->code) {
            $builder->join(
                'post_translations',
                fn ($on) => $on->on('posts.id', '=', 'post_translations.post_id')
                    ->where('post_translations.locale', $locale)
            );
        }

        return $builder;
    }

    public function addFrontendWithDefaults(array $with): array
    {
        $locale = app()->getLocale();

        if ($locale != Language::default()?->code) {
            $with['translations'] = fn ($q) => $q
                ->cacheFor(3600)
                ->where('locale', $locale);
        }

        return $with;
    }

    public function getPostBySlug($post, array $slug)
    {
        $locale = app()->getLocale();

        if (empty($post) && $locale != Language::default()->code) {
            $translation = PostTranslation::with(['post' => fn ($q) => $q->cacheFor(3600)])
                ->cacheFor(3600)
                ->where(['slug' => $slug[1], 'locale' => $locale])
                ->first();

            if ($translation) {
                $post = $translation->post;
            }

            return $post;
        }

        return $post;
    }
}
