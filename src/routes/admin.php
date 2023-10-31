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

Route::get('multilingual', [SettingController::class, 'index']);
Route::post('multilingual', [SettingController::class, 'save']);
