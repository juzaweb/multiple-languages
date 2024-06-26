<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\Multilang\Facades;

use Illuminate\Support\Facades\Facade;
use Juzaweb\Multilang\Contracts\Locale as LocaleContract;

class Locale extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return LocaleContract::class;
    }
}
