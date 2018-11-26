<?php

namespace DmitryIvanov\DarkSkyApi\Weather;

use DmitryIvanov\DarkSkyApi\Contracts\Weather\Alert as AlertContract;

class Alert implements AlertContract
{
    /**
     * The alert.
     *
     * @var array
     */
    protected $alert;

    /**
     * Create a new instance of the weather alert.
     *
     * @param  array  $alert
     * @return void
     */
    public function __construct(array $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Get the description.
     *
     * A detailed description of the alert.
     *
     * @return string|null
     */
    public function description()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'description');
    }

    /**
     * Get the expiration time.
     *
     * The UNIX time at which the alert will expire.
     *
     * @return int|null
     */
    public function expires()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'expires');
    }

    /**
     * Get the regions.
     *
     * An array of strings representing the names of the regions covered by this weather alert.
     *
     * @return array|null
     */
    public function regions()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'regions');
    }

    /**
     * Get the severity.
     *
     * Will take one of the following values:
     * "advisory" - an individual should be aware of potentially severe weather,
     * "watch" - an individual should prepare for potentially severe weather,
     * "warning" - an individual should take immediate action to protect themselves and others
     *             from potentially severe weather.
     *
     * @return string|null
     */
    public function severity()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'severity');
    }

    /**
     * Get the time.
     *
     * The UNIX time at which the alert was issued.
     *
     * @return int|null
     */
    public function time()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'time');
    }

    /**
     * Get the title.
     *
     * A brief description of the alert.
     *
     * @return string|null
     */
    public function title()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'title');
    }

    /**
     * Get the URI.
     *
     * An HTTP(S) URI that one may refer to for detailed information about the alert.
     *
     * @return string|null
     */
    public function uri()
    {
        return \DmitryIvanov\DarkSkyApi\array_get($this->alert, 'uri');
    }
}
