<?php
/**
 * JUZAWEB CMS - The Best CMS for Laravel Project
 *
 * @package    juzaweb/juzacms
 * @author     Juzaweb Team <admin@juzaweb.com>
 * @link       https://juzaweb.com
 * @license    GNU General Public License v2.0
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
                'in:session,subdomain,prefix',
            ],
            'mlla_subdomain' => [
                'required_if:mlla_type,==,subdomain',
                'array',
            ],
        ];
    }
}
