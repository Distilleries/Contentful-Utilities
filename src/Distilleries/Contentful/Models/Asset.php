<?php

namespace Distilleries\Contentful\Models;

use Illuminate\Database\Eloquent\Model;
use Distilleries\Contentful\Models\Traits\Localable;

/**
 * @property string $contentful_id
 * @property string $locale
 * @property string $country
 * @property string $title
 * @property string $description
 * @property string $url
 * @property string $file_name
 * @property string $content_type
 * @property integer $size
 * @property integer $width
 * @property integer $height
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Asset extends Model
{
    use Localable;

    /**
     * {@inheritdoc}
     */
    protected $table = 'assets';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'contentful_id',
        'locale',
        'country',
        'title',
        'description',
        'url',
        'file_name',
        'content_type',
        'size',
        'width',
        'height',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Return asset URL.
     *
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}
