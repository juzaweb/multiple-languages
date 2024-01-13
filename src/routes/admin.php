<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

use Juzaweb\Multilang\Http\Controllers\SettingController;
use Juzaweb\Multilang\Http\Controllers\LanguageController;

Route::get('multilingual', [SettingController::class, 'index']);
Route::post('multilingual', [SettingController::class, 'save']);

Route::group(
    ['prefix' => 'languages'],
    function () {
        Route::get('/', [LanguageController::class, 'index']);
        Route::post('/', [LanguageController::class, 'addLanguage']);
        Route::post('toggle-default', [LanguageController::class, 'toggleDefault'])
            ->name('admin.language.toggle-default');
    }
);
