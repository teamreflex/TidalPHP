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

class Endpoints
{
    const BASE      = 'https://api.tidalhifi.com/v1/';
    const API_TOKEN = 'P5Xbeo5LFvESeDy6';

    const LOGIN = self::BASE.'login/username?token='.self::API_TOKEN;

    const SEARCH = self::BASE.'search';

    const ARTIST_GET        = self::BASE.'artists/:id';
    const ARTIST_BIO        = self::ARTIST_GET.'/bio';
    const ARTIST_TOP_TRACKS = self::ARTIST_GET.'/toptracks';
    const ARTIST_VIDEOS     = self::ARTIST_GET.'/videos';
    const ARTIST_SIMILAR    = self::ARTIST_GET.'/similar';
    const ARTIST_ALBUMS     = self::ARTIST_GET.'/albums';

    const ALBUM_GET    = self::BASE.'albums/:id';
    const ALBUM_TRACKS = self::ALBUM_GET.'/tracks';

    const PLAYLIST_GET    = self::BASE.'playlists/:id';
    const PLAYLIST_TRACKS = self::PLAYLIST_GET.'/tracks';

    const TRACK_GET         = self::BASE.'tracks/:id';
    const TRACK_STREAM_URL  = self::TRACK_GET.'/streamUrl';
    const TRACK_OFFLINE_URL = self::TRACK_GET.'/offlineUrl';

    const VIDEO_GET        = self::BASE.'videos/:id';
    const VIDEO_STREAM_URL = self::VIDEO_GET.'/streamUrl';

    const USER_INFO = self::BASE.'users/:id';
    const ART_URL   = 'https://resources.wimpmusic.com/images/:key/:resx:res.jpg';
}
