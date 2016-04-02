<?php

namespace Tidal;

class Endpoints
{
	const BASE = "https://api.tidalhifi.com/v1/";
	const API_TOKEN = "P5Xbeo5LFvESeDy6";

	const LOGIN = self::BASE."login/username?token=".self::API_TOKEN;

	const SEARCH = self::BASE."search";

	const ARTIST_GET = self::BASE."artists/:id";
	const ARTIST_TOP_TRACKS = self::ARTIST_GET."/toptracks";

	const USER_INFO = self::BASE."users/:id";
}