<?php

/*
 * This file is apart of the TidalPHP project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Tidal\Parts\Extra;

use Tidal\Parts\Part;

class StreamUrl extends Part
{
    /**
     * {@inheritdoc}
     */
    protected $fillable = ['url', 'trackId', 'playTimeLeftInMinutes', 'soundQuality', 'encryptionKey'];
}
