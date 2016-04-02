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
use Clue\React\Buzz\Message\Response;
use Illuminate\Support\Collection;
use React\Dns\Resolver\Factory as DNSFactory;
use React\EventLoop\Factory as LoopFactory;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use Tidal\Parts\Album;
use Tidal\Parts\Artist;
use Tidal\Parts\Playlist;
use Tidal\Parts\Track;
use Tidal\Parts\Video;

class Tidal
{
    /**
     * The DNS resolver.
     *
     * @var \React\Dns\Resolver\Resolver The DNS resolver.
     */
    protected $dns;

    /**
     * The HTTP client.
     *
     * @var \Clue\React\Buzz\Browser|Tidal\Http The HTTP client.
     */
    protected $http;

    /**
     * Information about the user.
     *
     * @var array User information.
     */
    protected $userInfo;

    /**
     * Creates a new TIDAL client.
     *
     * @param LoopInterface $loop The ReactPHP event loop.
     * @param string        $dns  The DNS server to use.
     *
     * @return void
     */
    public function __construct(LoopInterface $loop = null, $dns = '8.8.8.8')
    {
        if (null === $loop) {
            $loop = LoopFactory::create();
        }

        $this->loop = $loop;
        $this->dns  = (new DNSFactory())->createCached($dns, $this->loop);
        $this->http = new Browser($this->loop);
    }

    /**
     * Authenticates with the TIDAL servers.
     *
     * @param string $email    The Email to login with.
     * @param string $password The password to login with.
     *
     * @return \React\Promise\Promise
     */
    public function connect($email, $password)
    {
        $deferred = new Deferred();

        $this->http->submit(Endpoints::LOGIN, [
            'username' => $email,
            'password' => $password,
        ])->then(function (Response $response) use ($deferred) {
            $json = json_decode($response->getBody(), true);

            $this->userInfo = $json;
            $this->http = new Http($this->http, $json);

            $deferred->resolve($this);
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }

    /**
     * Gets the current user information.
     *
     * @return \React\Promise\Promise
     */
    public function getUserInformation()
    {
        $deferred = new Deferred();

        $this->http->get(Options::replace(Endpoints::USER_INFO, ['id' => $this->userInfo['userId']]))->then(function ($json) use ($deferred) {
            $deferred->resolve($json);
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }

    /**
     * Runs a search query on the Discord servers.
     *
     * @param array $options An array of search options.
     *
     * @return \React\Promise\Promise
     */
    public function search(array $options)
    {
        $options = Options::buildOptions(Options::$defaultOptions, $options, $this);

        $deferred = new Deferred();

        $this->http->get(Endpoints::SEARCH.$options)->then(function ($response) use ($deferred) {
            $collection = new Collection();

            foreach ($response as $key => $values) {
                if ($key == 'artists') {
                    $artists = new Collection();

                    foreach ($values['items'] as $value) {
                        $artists->push(new Artist($this->http, $this, $value));
                    }

                    $collection['artists'] = $artists;
                } elseif ($key == 'albums') {
                    $albums = new Collection();

                    foreach ($values['items'] as $value) {
                        $albums->push(new Album($this->http, $this, $value));
                    }

                    $collection['albums'] = $albums;
                } elseif ($key == 'playlists') {
                    $playlists = new Collection();

                    foreach ($values['items'] as $value) {
                        $playlists->push(new Playlist($this->http, $this, $value));
                    }

                    $collection['playlists'] = $playlists;
                } elseif ($key == 'tracks') {
                    $tracks = new Collection();

                    foreach ($values['items'] as $value) {
                        $tracks->push(new Track($this->http, $this, $value));
                    }

                    $collection['tracks'] = $tracks;
                } elseif ($key == 'videos') {
                    $videos = new Collection();

                    foreach ($values['items'] as $value) {
                        $videos->push(new Video($this->http, $this, $value));
                    }

                    $collection['videos'] = $videos;
                }
            }

            $deferred->resolve($collection);
        }, function ($e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }

    /**
     * Runs the event loop.
     *
     * @return void
     */
    public function run()
    {
        $this->loop->run();
    }

    /**
     * Gets the user information.
     *
     * @return array User information.
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }
}
