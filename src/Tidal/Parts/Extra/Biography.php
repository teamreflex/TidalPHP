<?php

namespace Tidal\Parts\Extra;

use Carbon\Carbon;
use Tidal\Parts\Part;

class Biography extends Part
{
	/**
	 * {@inheritdoc}
	 */
	protected $fillable = ['source', 'lastUpdated', 'text', 'summary'];

	/**
	 * Gets the lastUpdated attribute.
	 *
	 * @return Carbon A carbon instance.
	 */
	public function getLastUpdatedAttribute()
	{
		return new Carbon($this->attributes['lastUpdated']);
	}
}