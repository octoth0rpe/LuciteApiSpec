<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

class Property implements SpecNodeInterface
{
    public string $name;
    public array $details;

    public function __construct(string $name, array $details = ['type' => 'string'])
    {
        $this->name = $name;
        $this->details = [
            'type' => $details['type'] ?? 'string',
            'enum' => $details['enum'] ?? null,
            'multipleOf' => $details['multipleOf'] ?? null,
            'maximum' => $details['maximum'] ?? null,
            'exclusiveMaximum' => $details['exclusiveMaximum'] ?? null,
            'minimum' => $details['minimum'] ?? null,
            'exclusiveMinimum' => $details['exclusiveMinimum'] ?? null,
            'maxLength' => $details['maxLength'] ?? null,
            'minLength' => $details['minLength'] ?? null,
            'pattern' => $details['pattern'] ?? null,
            'maxItems' => $details['maxItems'] ?? null,
            'minItems' => $details['minItems'] ?? null,
            'uniqueItems' => $details['uniqueItems'] ?? null,

            # TODO: figure out how to support these
            #'maxContains' => $details['maxContains'] ?? null,
            #'minContains' => $details['minContains'] ?? null,

            'maxProperties' => $details['maxProperties'] ?? null,
            'minProperties' => $details['minProperties'] ?? null,
        ];
        if (isset($details['const'])) {
            $this->details['const'] = $details['const'];
        }
    }

    public static function create(string $name, array $details = ['type' => 'string']): Property
    {
        return new Property($name, $details);
    }

    public function validate(array &$data): bool | string
    {
        $value = $data[$this->name] ?? null;
        if (is_string($value)) {
            $value = trim($value);
            $data[$this->name] = $value;
        }

        # Apply per type validation
        switch ($this->details['type']) {
            case 'null':
                if ($value !== null) {
                    return '{field} must be null';
                }
                # If the type is null, then no further validation checks can
                # be performed, so we can return true here.
                return true;
            case 'string':
                return static::validateString($value, $this->details);
            case 'boolean':
                return static::validateBoolean($value, $this->details);
            case 'array':
                return static::validateArray($value, $this->details);
            case 'number':
                return static::validateNumber($value, $this->details);
            case 'object':
                # TODO: figure out what kind of validation makes sense to implement
                return static::validateObject($value, $this->details);
            default:
                throw new \Exception('Unknown property type: '.$this->details['type']);
        }
        return true;
    }

    public static function validateArray(mixed $value, array $details): bool | string
    {
        if (is_array($value) === false) {
            return '{field} must be an array';
        }
        if (array_is_list($value) === false) {
            return '{field} must be an array';
        }
        $itemCount = count($value);
        if (isset($details['maxItems']) && $itemCount > $details['maxItems']) {
            return '{field} must contain at most '.$details['maxItems'].' items';
        }
        if (isset($details['minItems']) && $itemCount < $details['minItems']) {
            return '{field} must contain at least '.$details['minItems'].' items';
        }
        if (isset($details['uniqueItems']) && $details['uniqueItems'] === true) {
            $uniqueItemCount = count(array_unique($value));
            if ($uniqueItemCount < $itemCount) {
                return '{field} must contain at least '.$details['minItems'].' items';
            }
        }
        return true;
    }

    public static function validateString(mixed $value, array $details): bool | string
    {
        if (is_string($value) === false) {
            return '{field} must be a string';
        }
        $length = strlen($value);
        if (isset($details['minLength']) && $length < $details['minLength']) {
            return '{field} must be at least '.$details['minLength'].' characters long';
        }
        if (isset($details['maxLength']) && $length > $details['maxLength']) {
            return '{field} must be at most '.$details['maxLength'].' characters long';
        }
        if (isset($details['pattern'])) {
            $result = preg_match($details['pattern'], $value);
            if ($result === false || $result === 0) {
                return '{field} must match pattern '.$details['pattern'];
            }
        }
        if (isset($details['enum']) && in_array($value, $details['enum']) === false) {
            return '{field} must be one of '.implode(', ', $details['enum']);
        }
        if (isset($details['const']) && $value !== $details['const']) {
            return '{field} must be '.$details['const'];
        }
        return true;
    }

    public static function validateBoolean(mixed $value, array $details): bool | string
    {
        if (is_bool($value) === false) {
            return '{field} must be a boolean';
        }
        if (isset($details['enum']) && in_array($value, $details['enum']) === false) {
            return '{field} must be one of '.implode(', ', $details['enum']);
        }
        if (isset($details['const']) && $value !== $details['const']) {
            return '{field} must be '.$details['const'];
        }
        return true;
    }

    public static function validateNumber(mixed $value, array $details): bool | string
    {
        if (is_numeric($value) === false) {
            return '{field} must be a number';
        }
        if (isset($details['enum']) && in_array($value, $details['enum']) === false) {
            return '{field} must be one of '.implode(', ', $details['enum']);
        }
        if (isset($details['const']) && $value !== $details['const']) {
            return '{field} must be '.$details['const'];
        }
        if (isset($details['minimum']) && $value < $details['minimum']) {
            return '{field} must be greater than or equal to '.$details['minimum'];
        }
        if (isset($details['exclusiveMinimum']) && $value <= $details['exclusiveMinimum']) {
            return '{field} must be greater than '.$details['exclusiveMinimum'];
        }
        if (isset($details['maximum']) && $value > $details['maximum']) {
            return '{field} must be less than or equal to '.$details['maximum'];
        }
        if (isset($details['exclusiveMaximum']) && $value >= $details['exclusiveMaximum']) {
            return '{field} must be less than '.$details['exclusiveMaximum'];
        }
        if (isset($details['multipleOf']) && ($value % $details['multipleOf']) !== 0) {
            return '{field} must be a multiple of '.$details['multipleOf'];
        }
        return true;
    }

    public static function validateObject(mixed $value, array $details): bool | string
    {
        if (is_array($value) === false || array_is_list($value) === true) {
            return '{field} must be an object';
        }
        $keyCount = count(array_keys($value));
        if (isset($details['minProperties']) && $keyCount < $details['minProperties']) {
            return '{field} must have at least '.$details['minProperties'].' properties';
        }
        if (isset($details['maxProperties']) && $keyCount > $details['maxProperties']) {
            return '{field} must have at most '.$details['maxProperties'].' properties';
        }
        return true;
    }

    public function finalize(): array
    {
        return array_filter($this->details, function ($value) {
            return $value !== null;
        });
    }
}
