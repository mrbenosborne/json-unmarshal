# JSON Unmarshal
A PHP package for un-marshalling JSON data onto a class properties.

<div align="center">

![Build](https://github.com/mrbenosborne/json-unmarshal/workflows/PHP%20Composer/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/mrbenosborne/json-unmarshal/v)](//packagist.org/packages/mrbenosborne/json-unmarshal)
[![Latest Unstable Version](https://poser.pugx.org/mrbenosborne/json-unmarshal/v/unstable)](//packagist.org/packages/mrbenosborne/json-unmarshal)
[![License](https://poser.pugx.org/mrbenosborne/json-unmarshal/license)](//packagist.org/packages/mrbenosborne/json-unmarshal)
[![composer.lock](https://poser.pugx.org/mrbenosborne/json-unmarshal/composerlock)](//packagist.org/packages/mrbenosborne/json-unmarshal)

</div>

# Install
Install via composer.

```
composer require mrbenosborne/json-unmarshal
```

# Example
Below is an example of a Flight class, the full example can be found in the examples/ folder.

```php
<?php

use JSON\Attributes\JSON;
use JSON\Unmarshal;

include '../vendor/autoload.php';
include 'FlightRoute.php';

/**
 * Class Flight
 */
class Flight
{
    #[JSON(field: 'airline')]
    public string $airlineName;

    #[JSON(field: 'aircraft.type')]
    public string $aircraftType;

    #[JSON(field: 'route', type: FlightRoute::class)]
    public array $route;
}

// Create a new flight class
$flight = new Flight();

// Load our JSON data from file
$jsonData = json_decode(file_get_contents('flight.json'), true);

// Unmarshal JSON
Unmarshal::decode($flight, $jsonData);
```