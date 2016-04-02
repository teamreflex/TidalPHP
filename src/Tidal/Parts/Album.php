<?php

namespace Tidal\Parts;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tidal\Parts\Artist;
use Tidal\Parts\Part;

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
}