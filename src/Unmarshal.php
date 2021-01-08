<?php

namespace JSON;

use Exception;
use JSON\Attributes\JSON;
use ReflectionClass;
use ReflectionException;
use ReflectionType;

/**
 * Class Unmarshal.
 */
class Unmarshal
{
    /**
     * @param object $class
     * @param array  $data
     *
     * @throws Exception
     */
    public static function decode(object &$class, array $data): void
    {
        $reflectionClass = new ReflectionClass($class);
        foreach ($reflectionClass->getProperties() as $property) {
            $attributes = $property->getAttributes(JSON::class);
            foreach ($attributes as $attribute) {
                $jsonAttribute = $attribute->newInstance();
                $propertyType = $property->getType();
                if (is_null($propertyType)) {
                    continue;
                }

                /** @var ReflectionType $propertyType */
                switch ($propertyType->getName()) {
                    case 'string':
                        $class->{$property->name} = (string) self::getValueFromData(
                            $data,
                            $jsonAttribute->field,
                            $class->{$property->name} ?? '',
                        );
                        break;
                    case 'int':
                        $class->{$property->name} = (int) self::getValueFromData(
                            $data,
                            $jsonAttribute->field,
                            $class->{$property->name} ?? 0,
                        );
                        break;
                    case 'bool':
                        $class->{$property->name} = (bool) self::getValueFromData(
                            $data,
                            $jsonAttribute->field,
                            $class->{$property->name} ?? false,
                        );
                        break;
                    case 'float':
                        $class->{$property->name} = (float) self::getValueFromData(
                            $data,
                            $jsonAttribute->field,
                            $class->{$property->name} ?? 0,
                        );
                        break;
                    case 'array':
                        self::decodeArray($class, $property->name, $data, $property->name, $jsonAttribute->type);
                        break;
                    default:
                        self::decodeNonScalar(
                            $class,
                            $property->name,
                            $propertyType->getName(),
                            $data,
                            $jsonAttribute->field
                        );
                }
            }
        }
    }

    /**
     * @param object      $class
     * @param string      $propertyName
     * @param array       $data
     * @param string      $lookupFieldName
     * @param string|null $type
     *
     * @throws ReflectionException
     * @throws Exception
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    private static function decodeArray(
        object &$class,
        string $propertyName,
        array $data,
        string $lookupFieldName,
        ?string $type,
    ): void {
        if (is_null($type) || empty($type)) {
            throw new Exception('no type specified for array unmarshalling');
        }

        $items = self::getValueFromData(
            $data,
            $lookupFieldName,
            $class->{$propertyName} ?? [],
        );

        $class->{$propertyName} = [];
        foreach ($items as $item) {
            try {
                $object = new ReflectionClass($type);
            } catch (ReflectionException $exception) {
                throw $exception;
            }

            if ($object->isInstantiable()) {
                $unmarshalItem = $object->newInstance();
                self::decode($unmarshalItem, $item);
                $class->{$propertyName}[] = $unmarshalItem;
            }
        }
    }

    /**
     * @param array  $data
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    private static function getValueFromData(array $data, string $key, mixed $default): mixed
    {
        if (str_contains($key, '.')) {
            $keys = explode('.', $key, 2);

            return self::getValueFromData($data[$keys[0]], $keys[1], $default);
        }

        return $data[$key] ?? $default;
    }

    /**
     * @param object $class
     * @param string $propertyName
     * @param string $type
     * @param array  $data
     * @param string $lookupFieldName
     *
     * @throws ReflectionException
     *
     * @psalm-suppress ArgumentTypeCoercion
     */
    private static function decodeNonScalar(
        object &$class,
        string $propertyName,
        string $type,
        array $data,
        string $lookupFieldName
    ): void {
        // instantiated property
        if (isset($class->{$propertyName})) {
            self::decode($class->{$propertyName}, $data[$lookupFieldName]);

            return;
        }

        // not instantiated
        try {
            $object = new ReflectionClass($type);
        } catch (ReflectionException) {
            return;
        }

        if ($object->isInstantiable()) {
            $class->{$propertyName} = $object->newInstance();
            $value = self::getValueFromData($data, $lookupFieldName, null);
            if ($value) {
                self::decode($class->{$propertyName}, $data[$lookupFieldName]);
            }
        }
    }
}
