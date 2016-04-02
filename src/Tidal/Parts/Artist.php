<?php

namespace Tidal\Parts;

use Illuminate\Support\Collection;
use React\Promise\Deferred;
use Tidal\Endpoints;
use Tidal\Options;
use Tidal\Parts\Album;
use Tidal\Parts\Artist;
use Tidal\Parts\Extra\Biography;
use Tidal\Parts\Part;
use Tidal\Parts\Track;
use Tidal\Parts\Video;

class Artist extends Part
{
	/**
	 * {@inheritdoc}
	 */
	protected $fillable = ['id', 'name', 'url', 'picture', 'popularity'];

	/**
	 * Returns the artists top tracks.
	 *
	 * @param array $options An array of options.
	 * 
	 * @return \React\Promise\Promise A collection of top tracks.
	 */
	public function getTopTracks(array $options = [])
	{
		$deferred = new Deferred();

		$options = Options::buildOptions(Options::$defaultOptions, $options, $this->tidal);

		$this->http->get(
			Options::replace(Endpoints::ARTIST_TOP_TRACKS, ['id' => $this->id]).$options
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

	/**
	 * Returns the artists videos.
	 *
	 * @param array $options An array of options.
	 *
	 * @return \React\Promise\Promise A collection of top tracks.
	 */
	public function getVideos(array $options = [])
	{
		$deferred = new Deferred();

		$options = Options::buildOptions(Options::$defaultOptions, $options, $this->tidal);

		$this->http->get(
			Options::replace(Endpoints::ARTIST_VIDEOS, ['id' => $this->id]).$options
		)->then(function ($response) use ($deferred) {
			$videos = new Collection();

			foreach ($response['items'] as $video) {
				$videos->push(new Video(
					$this->http,
					$this->tidal,
					$video
				));
			}

			$deferred->resolve($videos);
		}, function ($e) use ($deferred) {
			$deferred->reject($e);
		});

		return $deferred->promise();
	}

	/**
	 * Gets the artists biography.
	 *
	 * @return \React\Promise\Promise The artist's biography.
	 */
	public function getBiography()
	{
		$deferred = new Deferred();

		$options = Options::buildOptions([], [], $this->tidal);

		$this->http->get(
			Options::replace(Endpoints::ARTIST_BIO, ['id' => $this->id]).$options
		)->then(function ($response) use ($deferred) {
			$deferred->resolve(new Biography($this->http, $this->tidal, $response));
		}, function ($e) use ($deferred) {
			$deferred->reject($e);
		});

		return $deferred->promise();
	}

	/**
	 * Gets similar artists.
	 *
	 * @param array $options An array of options.
	 * 
	 * @return \React\Promise\Promise The artist's similar artists.
	 */
	public function getSimilarArtists(array $options = [])
	{
		$deferred = new Deferred();

		$options = Options::buildOptions(Options::$defaultOptions, $options, $this->tidal);

		$this->http->get(
			Options::replace(Endpoints::ARTIST_SIMILAR, ['id' => $this->id]).$options
		)->then(function ($response) use ($deferred) {
			$artists = new Collection();

			foreach ($response['items'] as $artist) {
				$artists->push(new Artist(
					$this->http,
					$this->tidal,
					$artist
				));
			}

			$deferred->resolve($artists);
		}, function ($e) use ($deferred) {
			$deferred->reject($e);
		});

		return $deferred->promise();
	}

	/**
	 * Gets the artist's albums.
	 *
	 * @param array $options An array of options.
	 *
	 * @return \React\Promise\Promise The artist's albums.
	 */
	public function getAlbums(array $options = [])
	{
		$deferred = new Deferred();

		// For some reason, TIDAL returns 404 if the filter isn't caps.
		if (isset($options['filter'])) {
			$options['filter'] = strtoupper($options['filter']);
		}

		$options = Options::buildOptions(
			array_merge(Options::$defaultOptions, ['filter' => 'COMPILATIONS']),
			$options,
			$this->tidal
		);

		$this->http->get(
			Options::replace(Endpoints::ARTIST_ALBUMS, ['id' => $this->id]).$options
		)->then(function ($response) use ($deferred) {
			$albums = new Collection();

			foreach ($response['items'] as $album) {
				$albums->push(new Album(
					$this->http,
					$this->tidal,
					$album
				));
			}

			$deferred->resolve($albums);
		}, function ($e) use ($deferred) {
			$deferred->reject($e);
		});

		return $deferred->promise();
	}
}