<?php

namespace JSON\Attributes;

use Attribute;

/**
 * Class JSON
 * @package JSON\Attributes
 */
#[Attribute]
class JSON
{
    /**
     * JSON constructor.
     * @param  string  $field
     * @param  string|null  $type
     */
    public function __construct(
        public string $field,
        public ?string $type = null,
    ) {
    }
}