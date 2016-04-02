## TidalPHP

A PHP wrapper for the unofficial TIDAL API.

### Usage

```php
<?php

use Tidal\Tidal;

$tidal = new Tidal();

$tidal->connect('email', 'password')->then(function ($tidal) {
	echo "Connected.".PHP_EOL;
}, function ($e) {
	echo "There was an error connecting to TIDAL: {$e->getMessage()}".PHP_EOL;
});

$tidal->run();
```

### License

See [LICENSE.md](LICENSE.md).