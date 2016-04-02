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

class Album extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = ['id', 'title', 'duration', 'streamReady', 'streamStartDate', 'allowStreaming', 'premiumStreamOnly', 'numberOfTracks', 'numberOfVolumes', 'releaseDate', 'copyright', 'type', 'version', 'url', 'cover', 'explicit', 'upc', 'popularity', 'artists'];

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
     * Gets the releaseDate attribute.
     *
     * @return Carbon A carbon instance.
     */
    public function getReleaseDateAttribute()
    {
        return new Carbon($this->attributes['releaseDate']);
    }

    /**
     * Gets the cover URL attribute.
     *
     * @return string The cover URL.
     */
    public function getCoverUrlAttribute($res = 1280)
    {
        return str_replace('-', '/', Options::replace(Endpoints::ART_URL, [
            'key' => $this->cover,
            'res' => $res,
        ]));
    }

    /**
     * Gets the artists attribute.
     *
     * @return Collection A collection of artists.
     */
    public function getArtistsAttribute()
    {
        $new = new Collection();

        if (! isset($this->attributes['artists']) || ! is_array($this->attributes['artists'])) {
            return $new;
        }

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
     * Gets the albums tracks.
     *
     * @param array $options An array of options.
     *
     * @return \React\Promise\Promise The albums tracks.
     */
    public function getTracks(array $options = [])
    {
        $deferred = new Deferred();

        $options = Options::buildOptions(Options::$defaultOptions, $options, $this->tidal);

        $this->http->get(
            Options::replace(Endpoints::ALBUM_TRACKS, ['id' => $this->id]).$options
        )->then(function ($response) use ($deferred) {
            $tracks = new Collection();

            foreach ($response['items'] as $track) {
                $tracks->push(new Track(
                    $this->http,
                    $this->tidal,
                    $track
                ));
            }

            $deferred->resolve($tracks);
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }
}
