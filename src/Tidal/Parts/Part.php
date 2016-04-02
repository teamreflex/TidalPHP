<?php

/*
 * This file is apart of the TidalPHP project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Tidal\Parts;

use Illuminate\Support\Str;
use Tidal\Http;
use Tidal\Tidal;

class Part
{
    /**
     * The HTTP client.
     *
     * @var Http Cliemt.
     */
    protected $http;

    /**
     * The base TIDAL client.
     *
     * @var Tidal Client.
     */
    protected $tidal;

    /**
     * Holds the fillable attributes for the part.
     *
     * @var array The fillable attributes.
     */
    protected $fillable = [];

    /**
     * Holds the part attributes.
     *
     * @var array The attributes.
     */
    protected $attributes = [];

    /**
     * Constructs a part.
     *
     * @param Http  $http       The HTTP client.
     * @param Tidal $tidal      The TIDAL client.
     * @param array $attributes Attributes to fill.
     *
     * @return void
     */
    public function __construct(Http $http, Tidal $tidal, $attributes = [])
    {
        $this->http  = $http;
        $this->tidal = $tidal;
        $this->fill($attributes);
    }

    /**
     * Fills the part.
     *
     * @param array $attributes Attributes to fill.
     *
     * @return self
     */
    public function fill(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Handles dynamic get calls to the part.
     *
     * @param string $key The key.
     *
     * @return mixed
     */
    public function __get($key)
    {
        $funcName = 'get'.Str::studly($key).'Attribute';

        if (is_callable([$this, $funcName])) {
            return $this->{$funcName}();
        }

        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    /**
     * Handles dynamic set calls to the part.
     *
     * @param string $key   The key.
     * @param mixed  $value The value to set.
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $funcName = 'set'.Str::studly($key).'Attribute';

        if (is_callable([$this, $funcName])) {
            $this->attributes[$key] = $this->{$funcName}($value);

            return;
        }

        if (array_search($key, $this->fillable) === false) {
            return;
        }

        $this->attributes[$key] = $value;
    }

    /**
     * Gets the public attributes.
     *
     * @return array Public attributes.
     */
    public function getPublicAttributes()
    {
        $atts = [];

        foreach ($this->attributes as $key => $value) {
            $atts[$key] = $this->__get($key);
        }

        return $atts;
    }

    /**
     * Handles debug calls to the part.
     *
     * @return array An array of public attributes.
     */
    public function __debugInfo()
    {
        return $this->getPublicAttributes();
    }
}
