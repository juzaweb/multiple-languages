<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\Multilang\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'mlla_type' => [
                'required',
                'in:session,subdomain'
            ],
            'mlla_subdomain' => [
                'required_if:mlla_type,==,subdomain',
                'array'
            ],
        ];
    }
}
