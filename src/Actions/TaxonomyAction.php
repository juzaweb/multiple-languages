<?php

namespace Juzaweb\Multilang\Actions;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Abstracts\Action;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Models\TaxonomyTranslation;

class TaxonomyAction extends Action
{
    /**
     * Execute the actions.
     *
     * @return void
     */
    public function handle(): void
    {
        // Admin actions
        $this->addAction('taxonomies.form.left', [$this, 'addSelectLangTaxonomy'], 5);
        $this->addFilter('taxonomy.get-attribute', [$this, 'getTaxonomyAttribute'], 20, 3);
        $this->addFilter('taxonomy.getDataForForm', [$this, 'getTaxonomyDataForForm']);

        // Frontend actions
        $this->addFilter('taxonomy.withFrontendDefaults', [$this, 'addFrontendWithDefaults']);
        $this->addFilter('taxonomy.selectFrontendBuilder', [$this, 'changeFrontendQueryBuilder']);
        $this->addFilter('frontend.getTaxonomyBySlug', [$this, 'getTaxonomyBySlug'], 20, 2);
        $this->addAction('frontend.taxonomy.detail', [$this, 'showTaxonomyDetailFrontend']);
    }

    public function addSelectLangTaxonomy($model): void
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

    public function getTaxonomyAttribute($value, Taxonomy $post, string $key)
    {
        if (!in_array($key, (new TaxonomyTranslation)->getFillable())) {
            return $value;
        }

        $locale = app()->getLocale();

        if (!isset($post->translations) || !($post->translations instanceof Collection)) {
            return $value;
        }

        return $post->translations->where('locale', $locale)->first()[$key] ?? $value;
    }

    public function getTaxonomyDataForForm($data)
    {
        $locale = request()?->get('locale', mlla_default_language());

        $data['model']->fill(
            $data['model']->translations()->where('locale', $locale)->firstOrNew()->toArray()
        );

        return $data;
    }

    public function changeFrontendQueryBuilder(Builder $builder): Builder
    {
        if (mlla_enable() && ($locale = app()->getLocale())) {
            $builder->join(
                'taxonomy_translations',
                fn ($on) => $on->on('posts.id', '=', 'taxonomy_translations.post_id')
                    ->where('taxonomy_translations.locale', $locale)
            );
        }

        return $builder;
    }

    public function addFrontendWithDefaults(array $with): array
    {
        if (mlla_enable()) {
            $with['translations'] = fn ($q) => $q
                ->cacheFor(3600)
                ->where('locale', app()->getLocale());
        }

        return $with;
    }

    public function getTaxonomyBySlug($taxonomy, array $slug)
    {
        $locale = app()->getLocale();

        if (empty($taxonomy) && mlla_enable()) {
            $translation = TaxonomyTranslation::with(['taxonomy' => fn ($q) => $q->cacheFor(3600)])
                ->cacheFor(3600)
                ->where(['slug' => $slug[1], 'locale' => $locale])
                ->first();

            if ($translation) {
                $taxonomy = $translation->taxonomy;
            }

            return $taxonomy;
        }

        return $taxonomy;
    }

    public function showTaxonomyDetailFrontend(Taxonomy $taxonomy): void
    {
        // Disable post is not translated
        if (mlla_enable() && ($locale = app()->getLocale())) {
            abort_if(
                $taxonomy->locale != $locale && $taxonomy->translations->where('locale', $locale)->isEmpty(),
                404
            );
        }
    }
}
