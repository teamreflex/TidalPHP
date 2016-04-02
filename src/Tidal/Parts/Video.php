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

use Carbon\Carbon;
use Illuminate\Support\Collection;
use React\Promise\Deferred;
use Tidal\Endpoints;
use Tidal\Options;

class Video extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = ['id', 'title', 'releaseDate', 'imagePath', 'imageId', 'duration', 'quality', 'streamReady', 'streamStartDate', 'allowStreaming', 'explicit', 'popularity', 'type', 'artists'];

    /**
     * Gets the image URL attribute.
     *
     * @return string The image URL.
     */
    public function getImageUrlAttribute($res = 1280)
    {
        return str_replace('-', '/', Options::replace(Endpoints::ART_URL, [
            'key' => $this->imageId,
            'res' => $res,
        ]));
    }

    /**
     * Gets the releaseDate attribute.
     *
     * @return Carbon A carbon instance.
     */
    public function getReleaseDateAttribute()
    {
        return new Carbon($this->attributes['releaseDate']);
    }

    /**
     * Gets the streamStartDate attribute.
     *
     * @return Carbon A carbon instance.
     */
    public function getStreamStartDateAttribute()
    {
        return new Carbon($this->attributes['streamStartDate']);
    }

    /**
     * Gets the artists attribute.
     *
     * @return Collection A collection of artists.
     */
    public function getArtistsAttribute()
    {
        $new = new Collection();

        foreach ($this->attributes['artists'] as $artist) {
            $new->push(new Artist(
                $this->http,
                $this->tidal,
                $artist
            ));
        }

        return $new;
    }

    /**
     * Gets the stream URL.
     *
     * @return \React\Promise\Promise The stream URL.
     */
    public function getStreamUrl()
    {
        $deferred = new Deferred();

        $options = Options::buildOptions([], [], $this->tidal);

        $this->http->get(
            Options::replace(Endpoints::VIDEO_STREAM_URL, ['id' => $this->id]).$options
        )->then(function ($response) use ($deferred) {
            $deferred->resolve($response['url']);
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }
}
