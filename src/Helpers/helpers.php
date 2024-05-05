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

if (!function_exists('is_default_language')) {
    function is_default_language(string $locale): bool
    {
        return $locale == get_default_language();
    }
}

if (!function_exists('get_default_language')) {
    function get_default_language(): string
    {
        return Language::default()->code;
    }
}
