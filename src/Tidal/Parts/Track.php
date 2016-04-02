<?php

namespace Tidal\Parts;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use React\Promise\Deferred;
use Tidal\Endpoints;
use Tidal\Options;
use Tidal\Parts\Album;
use Tidal\Parts\Artist;
use Tidal\Parts\Extra\StreamUrl;
use Tidal\Parts\Part;

class Track extends Part
{
	/**
	 * Track streaming qualities.
	 *
	 * @var string Qualities.
	 */
	const QUALITY_LOSSLESS = 'LOSSLESS';
	const QUALITY_HIGH     = 'HIGH';
	const QUALITY_LOW      = 'LOW';

	/**
	 * {@inheritdoc}
	 */
	protected $fillable = ['id', 'title', 'duration', 'replayGain', 'peak', 'allowStreaming', 'streamReady', 'streamStartDate', 'premiumStreamingOnly', 'trackNumber', 'volumeNumber', 'version', 'popularity', 'copyright', 'url', 'isrc', 'explicit', 'artists', 'album'];

	/**
	 * Gets the cover URL attribute.
	 *
	 * @return string The cover URL.
	 */
	public function getCoverUrlAttribute($res = 1280)
	{
		return str_replace('-', '/', Options::replace(Endpoints::ART_URL, [
			'key' => $this->album->cover,
			'res' => $res,
		]));
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

	/**
	 * Gets the track streaming URL.
	 *
	 * @return \React\Promise\Promise The streaming URL.
	 */
	public function getStreamUrl($quality = null)
	{
		$deferred = new Deferred();

		$valid = [self::QUALITY_LOSSLESS, self::QUALITY_HIGH, self::QUALITY_LOW];

		if (array_search($quality, $valid) === false) {
			$quality = self::QUALITY_HIGH;
		}

		$options = Options::buildOptions([
			'soundQuality' => $quality,
		], [], $this->tidal);

		$this->http->get(
			Options::replace(Endpoints::TRACK_STREAM_URL, ['id' => $this->id]).$options
		)->then(function ($response) use ($deferred) {
			$deferred->resolve(new StreamUrl($this->http, $this->tidal, $response));
		}, function ($e) use ($deferred) {
			$deferred->reject($e);
		});

		return $deferred->promise();
	}

	/**
	 * Gets the track offline URL.
	 *
	 * @return \React\Promise\Promise The offline URL.
	 */
	public function getOfflineUrl($quality = null)
	{
		$deferred = new Deferred();

		$valid = [self::QUALITY_LOSSLESS, self::QUALITY_HIGH, self::QUALITY_LOW];

		if (array_search($quality, $valid) === false) {
			$quality = self::QUALITY_HIGH;
		}

		$options = Options::buildOptions([
			'soundQuality' => $quality,
		], [], $this->tidal);

		$this->http->get(
			Options::replace(Endpoints::TRACK_OFFLINE_URL, ['id' => $this->id]).$options
		)->then(function ($response) use ($deferred) {
			$deferred->resolve(new StreamUrl($this->http, $this->tidal, $response));
		}, function ($e) use ($deferred) {
			$deferred->reject($e);
		});

		return $deferred->promise();
	}
}