<?php

namespace Tidal\Parts;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tidal\Parts\Album;
use Tidal\Parts\Artist;
use Tidal\Parts\Part;

class Track extends Part
{
	/**
	 * {@inheritdoc}
	 */
	protected $fillable = ['id', 'title', 'duration', 'replayGain', 'peak', 'allowStreaming', 'streamReady', 'streamStartDate', 'premiumStreamingOnly', 'trackNumber', 'volumeNumber', 'version', 'popularity', 'copyright', 'url', 'isrc', 'explicit', 'artists', 'album'];

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
	 * Gets the album attribute.
	 *
	 * @return Album The album.
	 */
	public function getAlbumAttribute()
	{
		return new Album(
			$this->http,
			$this->tidal,
			$this->attributes['album']
		);
	}
}