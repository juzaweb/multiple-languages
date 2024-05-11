<?php
// @formatter:off
// phpcs:ignoreFile

namespace Juzaweb\Backend\Models {
    use Awobaz\Compoships\Database\Eloquent\Relations\HasMany;use Illuminate\Database\Eloquent\Builder;
    use Illuminate\Database\Eloquent\Collection;
    use Juzaweb\Multilang\Models\PostTranslation;
    /**
     * @method Builder|HasMany translations
     * @property-read Collection<PostTranslation> $translations
     */
    class Post {}
}
