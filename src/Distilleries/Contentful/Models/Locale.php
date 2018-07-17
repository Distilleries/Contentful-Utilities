<?php

namespace Distilleries\Contentful\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @property integer $id
 * @property string $label
 * @property string $code
 * @property string $fallback_code
 * @property boolean $is_editable
 * @property boolean $is_publishable
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Locale extends Model
{
    /**
     * {@inheritdoc}
     */
    protected $table = 'locales';

    /**
     * {@inheritdoc}
     */
    protected $fillable = [
        'label',
        'code',
        'locale',
        'country',
        'fallback_code',
        'is_default',
        'is_editable',
        'is_publishable',
    ];

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'is_default' => 'boolean',
        'is_editable' => 'boolean',
        'is_publishable' => 'boolean',
    ];

    /**
     * Return default locale code.
     *
     * @return string
     */
    public static function default(): string
    {
        $default = Cache::get('locale_default');

        if ($default === null)
        {
            $default = static::query()
                ->select('locale')
                ->where('is_default', '=', true)
                ->first();

            $default = !empty($default) ? $default->locale : config('contentful.default_locale');
            // Cache is cleaned in Console\Commands\SyncLocales (run at least daily)
            Cache::forever('locale_default', $default);
        }

        return $default;
    }


    public static function getAppOrDefaultLocale(): string
    {
        return app()->getLocale() ?? self::default();
    }

    public static function getAppOrDefaultCountry($key = 'app.country'): string
    {
        return config($key, self::defaultCountry());
    }

    /**
     * Return default country code.
     *
     * @return string
     */
    public static function defaultCountry(): string
    {
        $default = Cache::get('country_default');

        if ($default === null)
        {
            $default = static::query()
                ->select('country')
                ->where('is_default', '=', true)
                ->first();
            $default = !empty($default) ? $default->country : config('contentful.default_country');
            // Cache is cleaned in Console\Commands\SyncLocales (run at least daily)
            Cache::forever('country_default', $default);
        }

        return $default;
    }

    /**
     * Return fallback code for given locale code.
     *
     * @param  string $code
     * @return string
     */
    public static function fallback(string $code): string
    {
        $fallback = Cache::get('locale_fallback_' . $code);

        if ($fallback === null)
        {
            $locale = static::query()
                ->select('fallback_code')
                ->where('code', '=', $code)
                ->first();

            $fallback = (!empty($locale) and !empty($locale->fallback_code)) ? $locale->fallback_code : '';

            Cache::put('locale_fallback_' . $code, $fallback, 5);
        }

        return $fallback;
    }

    public static function canBeSave(string $country, string $locale): bool
    {
        $locales = config('contentful.locales_not_flatten', '');
        $locales = explode(',', $locales);
        return !in_array($country . '_' . $locale, $locales);
    }

    public static function getLocale(string $locale): string
    {
        if (Str::contains($locale, '_'))
        {
            $tab = explode('_', $locale);
            return $tab[1];
        }
        else if (Str::contains($locale, '-'))
        {
            $tab = explode('-', $locale);
            return $tab[1];
        }

        return $locale;
    }

    public static function getCountry(string $locale): string
    {
        if (Str::contains($locale, '_'))
        {
            $tab = explode('_', $locale);
            return $tab[0];
        } else if (Str::contains($locale, '-'))
        {
            $tab = explode('-', $locale);
            return $tab[0];
        }

        return config('contentful.default_country');
    }


}
