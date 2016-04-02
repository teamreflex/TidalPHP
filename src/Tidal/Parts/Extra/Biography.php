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
