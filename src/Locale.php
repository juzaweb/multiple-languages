<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\Multilang;

use Juzaweb\CMS\Contracts\ConfigContract;
use Juzaweb\CMS\Models\Language;
use Juzaweb\CMS\Support\Application;

class Locale implements Contracts\Locale
{
    public function __construct(protected Application $app)
    {
    }

    public function setLocale($locale = null): ?string
    {
        if ($this->app[ConfigContract::class]->getConfig('mlla_type') !== 'prefix') {
            return $locale;
        }

        if (empty($locale)) {
            $locale = $this->app['request']->segment(1);
        }

        if (empty($locale) || !\is_string($locale) || !Language::languages()->has($locale)) {
            return null;
        }

        return $locale;
    }
}
