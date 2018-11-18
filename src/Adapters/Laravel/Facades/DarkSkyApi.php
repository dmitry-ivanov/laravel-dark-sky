<?php

namespace DmitryIvanov\DarkSkyApi\Adapters\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use DmitryIvanov\DarkSkyApi\Contracts\Service;

/**
 * @method static Service location(float $latitude, float $longitude)
 * @method static Service units(string $units)
 * @method static Service language(string $language)
 * @method static Service extend(string $blocks)
 * @method static array forecast(array|string|null $blocks = null)
 * @method static array timeMachine(array|string $dates, array|string|null $blocks = null)
 *
 * @see \DmitryIvanov\DarkSkyApi\Contracts\Service
 */
class DarkSkyApi extends Facade
{
    /**
     * Get the facade accessor.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return Service::class;
    }
}
