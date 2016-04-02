<?php

namespace Tidal\Parts;

use Carbon\Carbon;
use Tidal\Parts\Part;

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
}