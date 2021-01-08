<?php

namespace Tests\Data;

use JSON\Attributes\JSON;

/**
 * Class ChildClass
 * @package Tests\Data
 */
class ChildClass
{
    /**
     * @var int
     */
    #[JSON('age')]
    public int $age;

    /**
     * @var string
     */
    #[JSON('name')]
    public string $name;
}