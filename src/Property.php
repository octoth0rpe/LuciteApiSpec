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
            'maxContains' => $details['maxContains'] ?? null,
            'minContains' => $details['minContains'] ?? null,
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

        # Validate type
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
                return true;
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
        if (isset($details['enum']) && in_array($value, $details['enum'])) {
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
        if (isset($details['enum']) && in_array($value, $details['enum'])) {
            return '{field} must be one of '.implode(', ', $details['enum']);
        }
        if (isset($details['const']) && $value !== $details['const']) {
            return '{field} must be '.$details['const'];
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
