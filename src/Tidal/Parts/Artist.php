<?php

namespace Tidal\Parts;

use Illuminate\Support\Collection;
use React\Promise\Deferred;
use Tidal\Endpoints;
use Tidal\Options;
use Tidal\Parts\Part;
use Tidal\Parts\Track;

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
	 * @return Collection A collection of top tracks.
	 */
	public function getTopTracks(array $options = [])
	{
		$deferred = new Deferred();

		$options = Options::buildOptions([
			'limit'  => -1,
			'filter' => 'all',
			'offset' => 0
		], $options, $this->tidal);

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
}