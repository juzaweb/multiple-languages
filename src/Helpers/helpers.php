<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

use Juzaweb\CMS\Models\Language;

if (!function_exists('mlla_is_default_language')) {
    function mlla_is_default_language(string $locale): bool
    {
        return $locale == mlla_default_language();
    }
}

if (!function_exists('mlla_default_language')) {
    function mlla_default_language(): string
    {
        return Language::default()->code;
    }
}

if (!function_exists('mlla_enable')) {
    function mlla_enable(): bool
    {
        return !empty(get_config('mlla_type'));
    }
}
