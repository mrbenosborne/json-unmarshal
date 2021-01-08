<?php

namespace Tests\Unit;

use Exception;
use JSON\Attributes\JSON;
use JSON\Unmarshal;
use PHPUnit\Framework\TestCase;
use Tests\Data\ChildClass;
use Tests\Data\ParentClass;

/**
 * Class UnmarshalTest
 */
class UnmarshalTest extends TestCase
{
    /**
     * Given:   A class that has properties that are
     *          scalars.
     *  and:    The property has an attribute of JSON.
     * When:    The class is passed to Unmarshal::decode() along
     *          with an array of data.
     * Then:    The properties are set with their matching data.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalScalars(): void
    {
        $input = new class {
            #[JSON('age')]
            public int $age;

            #[JSON('first_name')]
            public string $firstName;

            #[JSON('is_human')]
            public bool $isHuman;

            #[JSON('latitude')]
            public float $latitude;
        };

        Unmarshal::decode(
            $input,
            [
                'age' => 1,
                'first_name' => 'John Doe',
                'is_human' => false,
                'latitude' => 1.1234567,
            ]
        );
        $this->assertEquals(1, $input->age);
        $this->assertEquals('John Doe', $input->firstName);
        $this->assertFalse($input->isHuman);
        $this->assertEquals(1.1234567, $input->latitude);
    }

    /**
     * Given:   A class that has properties.
     *  and:    The property has an attribute of JSON and one
     *          property does not.
     * When:    The class is passed to Unmarshal::decode() along
     *          with an array of data.
     * Then:    The property with the attribute is set with the
     *          matching data.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalOneField(): void
    {
        $input = new class {
            #[JSON('age')]
            public int $age;
            public string $firstName = '';
        };

        Unmarshal::decode(
            $input,
            [
                'age' => 1,
                'first_name' => '',
            ]
        );
        $this->assertEquals(1, $input->age);
        $this->assertEmpty($input->firstName);
    }

    /**
     * Given:   A class that has properties.
     *  and:    The property has an attribute of JSON.
     *  and:    The attributes value is not in the array of data.
     * When:    The class is passed to Unmarshal::decode() along
     *          with an array of data.
     * Then:    The property remains empty.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalUnMatchedField(): void
    {
        $input = new class {
            #[JSON('age')]
            public int $age = 0;
        };

        Unmarshal::decode($input, []);
        $this->assertEquals(0, $input->age);
    }

    /**
     * Given:   A class that has a property and its type
     *          is an instantiable.
     *  and:    The property has an attribute of JSON.
     * When:    Unmarshal::decode() is called.
     * Then:    Each property is instantiated if it is not
     *          already.
     *  and:    The data, if found is set against each property.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalObject(): void
    {
        $input = new ParentClass();
        Unmarshal::decode(
            $input,
            [
                'first_name' => 'Foo',
                'child' => [
                    'age' => 1,
                ],
            ]
        );
        $this->assertEquals('Foo', $input->firstName);
        $this->assertEquals(1, $input->child->age);
    }

    /**
     * Given:   A class that has a property and its type
     *          is instantiable.
     *  and:    The property has an attribute of JSON.
     *  and:    The property is already instantiated.
     * When:    Unmarshal::decode() is called.
     * Then:    The data, if found is set against each property
     *  and:    The existing class is returned, a new one is not created.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalObjectAlreadyInstantiated(): void
    {
        $input = new ParentClass();
        $input->child = new ChildClass();
        $input->child->name = 'Bar';

        Unmarshal::decode(
            $input,
            [
                'first_name' => 'Foo',
                'child' => [
                    'age' => 1,
                ],
            ]
        );
        $this->assertEquals('Foo', $input->firstName);
        $this->assertEquals(1, $input->child->age);
        $this->assertEquals('Bar', $input->child->name);
    }

    /**
     * Given:   A class that has a property.
     *  and:    The property has an attribute of JSON.
     *  and:    The attributes value uses the dot syntax
     *          to access an object.
     *
     * When:    The class is passed to Unmarshal::decode() along
     *          with an array of data.
     *  and:    The data has an object with a field.
     *
     * Then:    The property with the attribute is set with the
     *          matching data.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalObjectWithDotSyntax(): void
    {
        $input = new class {
            #[JSON('stats.hits')]
            public int $hits;
        };

        Unmarshal::decode(
            $input,
            [
                "stats" => [
                    "hits" => 1,
                ],
            ]
        );
        $this->assertEquals(1, $input->hits);
    }

    /**
     * Given:   A class that has a property.
     *  and:    The property has an attribute of JSON.
     *  and:    The attributes field value is set but the type is
     *          not set.
     *
     * When:    The class is passed to Unmarshal::decode() along
     *          with an array of data.
     *
     * Then:    An exception is thrown.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalArrayWithoutType(): void
    {
        $input = new class {
            #[JSON('people')]
            public array $people;
        };

        $this->expectExceptionMessage('no type specified for array unmarshalling');
        Unmarshal::decode(
            $input,
            [
                "people" => [
                    [
                        'first_name' => 'Foo',
                    ],
                    [
                        'first_name' => 'Bar',
                    ],
                ],
            ]
        );
    }

    /**
     * Given:   A class that has a property.
     *  and:    The property has an attribute of JSON.
     *  and:    The attributes field value is set and so is the type.
     *
     * When:    The class is passed to Unmarshal::decode() along
     *          with an array of data.
     *  and:    The data is an array of array.
     *
     * Then:    The property with the attribute is set with the
     *          matching data and type.
     *
     * @return void
     * @throws Exception
     */
    public function testUnmarshalArrayWithType(): void
    {
        $input = new class {
            #[JSON('people', ParentClass::class)]
            public array $people;
        };

        Unmarshal::decode(
            $input,
            [
                "people" => [
                    [
                        'first_name' => 'Foo',
                    ],
                    [
                        'first_name' => 'Bar',
                    ],
                ],
            ]
        );
        $this->assertCount(2, $input->people);
    }
}