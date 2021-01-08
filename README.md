# JSON Unmarshal
A PHP package for un-marshalling JSON data onto a class properties.

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
    /**
     * @var string
     */
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