<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\Multilang\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Juzaweb\CMS\Models\Language;
use Juzaweb\Multilang\Models\TaxonomyTranslation;

class TaxonomyObserver
{
    protected static array $translationFileds;

    public function saving(Model $model): void
    {
        self::$translationFileds = [
            'locale' => $model->locale,
            'fileds' => Arr::only($model->getAttributes(), (new TaxonomyTranslation)->getFillable()),
        ];

        if ($model->locale == Language::default()?->code) {
            $model->offsetUnset('locale');
        } else {
            foreach ((new TaxonomyTranslation)->getFillable() as $item) {
                $model->offsetUnset($item);
            }
        }
    }

    public function saved(Model $model): void
    {
        if (!Arr::hasAny(
            self::$translationFileds['fileds'],
            [
                'name',
                'description',
                'thumbnail',
            ])
        ) {
            return;
        }

        $model->translations()->updateOrCreate(
            [
                'locale' => self::$translationFileds['locale'],
                'taxonomy_id' => $model->id,
            ],
            self::$translationFileds['fileds']
        );
    }
}
