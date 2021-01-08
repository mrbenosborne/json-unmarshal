<?php

namespace Tests\Data;

use JSON\Attributes\JSON;

/**
 * Class ParentClass
 * @package Tests\Data
 */
class ParentClass
{
    #[JSON('first_name')]
    public string $firstName;

    #[JSON('child')]
    public ChildClass $child;
}