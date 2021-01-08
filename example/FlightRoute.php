<?php

use JSON\Attributes\JSON;

/**
 * Class FlightRoute
 */
class FlightRoute
{
    #[JSON('sequence')]
    public int $sequence;

    #[JSON('cost')]
    public float $cost;

    #[JSON('luggageIncluded')]
    public bool $luggageIncluded;

    #[JSON('airline')]
    public string $airline;

    #[JSON('departureAirport')]
    public string $departureAirport;

    #[JSON('arrivalAirport')]
    public string $arrivalAirport;
}