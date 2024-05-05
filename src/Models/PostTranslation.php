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
use Juzaweb\Backend\Models\Post;
use Juzaweb\CMS\Models\Model;

class PostTranslation extends Model
{
    protected $table = 'post_translations';

    protected $fillable = [
        'title',
        'content',
        'description',
        'thumbnail',
        'slug',
        'locale',
        'post_id',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }
}
