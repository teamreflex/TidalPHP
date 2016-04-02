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

use Clue\React\Buzz\Browser;
use Clue\React\Buzz\Message\Request;
use Clue\React\Buzz\Message\Response;
use React\Promise\Deferred;

class Http
{
    protected $browser;
    protected $userInfo;

    /**
     * Constructs a HTTP client wrapper.
     *
     * @param \Clue\React\Buzz\Browser $browser  The browser.
     * @param array                    $userInfo Information about the user.
     *
     * @return void
     */
    public function __construct(Browser $browser, $userInfo)
    {
        $this->browser  = $browser;
        $this->userInfo = $userInfo;
    }

    public function __call($func, array $args = [])
    {
        @list($uri, $body, $headers, $auth) = $args;

        if (null === $body) {
            $body = [];
        }

        if (null === $headers) {
            $headers = [];
        }

        if (null === $auth) {
            $auth = true;
        }

        $body = json_encode($body);

        $deferred = new Deferred();

        if ($auth) {
            $headers['X-Tidal-SessionId'] = $this->userInfo['sessionId'];
        }

        $this->browser->send(
            new Request(strtoupper($func), $uri, $headers, $body)
        )->then(function (Response $response) use ($deferred) {
            if (($json = json_decode($response->getBody(), true)) !== null) {
                $deferred->resolve($json);

                return;
            }

            $deferred->resolve($response);
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }
}
