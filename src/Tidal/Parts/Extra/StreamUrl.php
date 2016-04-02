<?php

namespace Tidal\Parts\Extra;

use Tidal\Parts\Part;

class StreamUrl extends Part
{
	/**
	 * {@inheritdoc}
	 */
	protected $fillable = ['url', 'trackId', 'playTimeLeftInMinutes', 'soundQuality', 'encryptionKey'];
}