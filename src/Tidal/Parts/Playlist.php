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

class Playlist extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = ['uuid', 'title', 'numberOfTracks', 'numberOfVideos', 'creator', 'description', 'duration', 'lastUpdated', 'created', 'type', 'publicPlaylist', 'url', 'image', 'popularity'];

    /**
     * Gets the lastUpdated attribute.
     *
     * @return Carbon A carbon instance.
     */
    public function getLastUpdatedAttribute()
    {
        return new Carbon($this->attributes['lastUpdated']);
    }

    /**
     * Gets the created attribute.
     *
     * @return Carbon A carbon instance.
     */
    public function getCreatedAttribute()
    {
        return new Carbon($this->attributes['created']);
    }

    /**
     * Gets the playlists tracks.
     *
     * @param array $options An array of options.
     *
     * @return \React\Promise\Promise The playlists tracks.
     */
    public function getTracks(array $options = [])
    {
        $deferred = new Deferred();

        $options = Options::buildOptions(Options::$defaultOptions, $options, $this->tidal);

        $this->http->get(
            Options::replace(Endpoints::PLAYLIST_TRACKS, ['id' => $this->uuid]).$options
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
