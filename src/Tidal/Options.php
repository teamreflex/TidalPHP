<?php

/*
 * This file is apart of the TidalPHP project.
 *
 * Copyright (c) 2016 David Cole <david@team-reflex.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the LICENSE.md file.
 */

namespace Tidal;

class Options
{
    /**
     * The default options for queries.
     *
     * @var array Default options.
     */
    public static $defaultOptions = ['limit' => 999, 'filter' => 'all', 'offset' => 0];

    /**
     * Builds a query URL.
     *
     * @param array $baseOptions  The base options.
     * @param array $extraOptions The extra options.
     * @param Tidal $tidal        The TIDAL client.
     *
     * @return string The encoded options.
     */
    public static function buildOptions(array $baseOptions, array $extraOptions, Tidal $tidal)
    {
        $options                = array_merge($baseOptions, $extraOptions);
        $options['countryCode'] = $tidal->getUserInfo()['countryCode'];

        return '?'.http_build_query($options);
    }

    /**
     * Replaces placeholders in a string with variables.
     *
     * @param string $string The string work with.
     * @param array  $vars   The options to fill in.
     *
     * @return string The final string.
     */
    public static function replace($string, array $vars)
    {
        if (preg_match_all('/:([a-z_]+)/', $string, $matches)) {
            list($original, $variables) = $matches;

            foreach ($variables as $key => $var) {
                if (isset($vars[$var])) {
                    $string = str_replace($original[$key], $vars[$var], $string);
                }
            }
        }

        return $string;
    }
}
