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

// ----------
// Print data
// ----------
echo $flight->airlineName.PHP_EOL; // Foo Airlines
echo $flight->aircraftType.PHP_EOL; // Boeing 747

echo PHP_EOL;
echo 'Route:'.PHP_EOL;

/** @var FlightRoute $route */
foreach ($flight->route as $route) {
    echo '------'.PHP_EOL;
    echo $route->sequence.PHP_EOL;
    echo $route->cost.PHP_EOL;
    echo var_export($route->luggageIncluded, true).PHP_EOL;
    echo $route->airline.PHP_EOL;
    echo $route->departureAirport.PHP_EOL;
    echo $route->arrivalAirport.PHP_EOL;
}