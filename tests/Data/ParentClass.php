<?php

namespace Tests\Data;

use JSON\Attributes\JSON;

/**
 * Class ParentClass.
 */
class ParentClass
{
    #[JSON('first_name')]
    public string $firstName;

    #[JSON('child')]
    public ChildClass $child;
}
