<?php

namespace Tidal\Parts;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Tidal\Parts\Artist;
use Tidal\Parts\Part;

class Video extends Part
{
	/**
	 * {@inheritdoc}
	 */
	protected $attributes = ['id', 'title', 'releaseDate', 'imagePath', 'imageId', 'duration', 'quality', 'streamReady', 'streamStartDate', 'allowStreaming', 'explicit', 'popularity', 'type', 'artists'];

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
}