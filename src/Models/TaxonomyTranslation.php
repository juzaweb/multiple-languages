<?php
/**
 * JUZAWEB CMS - Laravel CMS for Your Project
 *
 * @package    juzaweb/cms
 * @author     The Anh Dang
 * @link       https://juzaweb.com
 * @license    GNU V2
 */

namespace Juzaweb\Multilang\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Juzaweb\Backend\Models\Taxonomy;
use Juzaweb\CMS\Models\Model;

class TaxonomyTranslation extends Model
{
    protected $table = 'taxonomy_translations';

    protected $fillable = [
        'name',
        'thumbnail',
        'description',
        'slug',
        'locale',
    ];

    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id', 'id');
    }
}
