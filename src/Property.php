<?php

declare(strict_types=1);

namespace Lucite\ApiSpec;

use Lucite\ApiSpec\Schema;

class Property implements SpecNodeInterface
{
    public string $name;
    public string $type;
    public array $rules;
    public bool $required;
    public bool $readOnly;
    public bool $writeOnly;
    public bool $primaryKey;
    public ?Schema $parent;

    public function __construct(string $name, string $type = 'string', array $rules = [], bool $required = false, bool $readOnly = false, bool $writeOnly = false, bool $primaryKey = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->rules = is_array($rules) ? $rules : [];
        $this->required = $required;
        $this->readOnly = $readOnly || $primaryKey;
        $this->writeOnly = $writeOnly;
        $this->primaryKey = $primaryKey;
    }

    public function validate(array &$data): bool | string
    {
        if ($this->required === false && array_key_exists($this->name, $data) === false) {
            return true;
        }
        $value = $data[$this->name] ?? null;
        if (is_string($value)) {
            $value = trim($value);
            $data[$this->name] = $value;
        }

        if ($this->readOnly) {
            unset($data[$this->name]);
            return true;
        }

        # Apply per type validation
        switch ($this->type) {
            case 'null':
                if ($value !== null) {
                    return '{field} must be null';
                }
                # If the type is null, then no further validation checks can
                # be performed, so we can return true here.
                return true;
            case 'string':
                return static::validateString($value, $this->rules);
            case 'boolean':
                return static::validateBoolean($value, $this->rules);
            case 'array':
                return static::validateArray($value, $this->rules);
            case 'number':
                return static::validateNumber($value, $this->rules);
            case 'object':
                # TODO: figure out what kind of validation makes sense to implement
                return static::validateObject($value, $this->rules);
            default:
                throw new \Exception('Unknown property type: '.$this->type);
        }
        return true;
    }

    public static function validateArray(mixed $value, array $rules): bool | string
    {
        if (is_array($value) === false) {
            return '{field} must be an array';
        }
        if (array_is_list($value) === false) {
            return '{field} must be an array';
        }
        $itemCount = count($value);
        if (isset($rules['maxItems']) && $itemCount > $rules['maxItems']) {
            return '{field} must contain at most '.$rules['maxItems'].' items';
        }
        if (isset($rules['minItems']) && $itemCount < $rules['minItems']) {
            return '{field} must contain at least '.$rules['minItems'].' items';
        }
        if (isset($rules['uniqueItems']) && $rules['uniqueItems'] === true) {
            $uniqueItemCount = count(array_unique($value));
            if ($uniqueItemCount < $itemCount) {
                return '{field} must contain at least '.$rules['uniqueItems'].' items';
            }
        }
        return true;
    }

    public static function validateString(mixed $value, array $rules): bool | string
    {
        if (is_string($value) === false) {
            return '{field} must be a string';
        }
        $length = strlen($value);
        if (isset($rules['minLength']) && $length < $rules['minLength']) {
            return '{field} must be at least '.$rules['minLength'].' characters long';
        }
        if (isset($rules['maxLength']) && $length > $rules['maxLength']) {
            return '{field} must be at most '.$rules['maxLength'].' characters long';
        }
        if (isset($rules['pattern'])) {
            $result = preg_match($rules['pattern'], $value);
            if ($result === false || $result === 0) {
                return '{field} must match pattern '.$rules['pattern'];
            }
        }
        if (isset($rules['enum']) && in_array($value, $rules['enum']) === false) {
            return '{field} must be one of '.implode(', ', $rules['enum']);
        }
        if (isset($rules['const']) && $value !== $rules['const']) {
            return '{field} must be '.$rules['const'];
        }
        return true;
    }

    public static function validateBoolean(mixed $value, array $rules): bool | string
    {
        if (is_bool($value) === false) {
            return '{field} must be a boolean';
        }
        if (isset($rules['enum']) && in_array($value, $rules['enum']) === false) {
            return '{field} must be one of '.implode(', ', $rules['enum']);
        }
        if (isset($rules['const']) && $value !== $rules['const']) {
            return '{field} must be '.$rules['const'];
        }
        return true;
    }

    public static function validateNumber(mixed $value, array $rules): bool | string
    {
        if (is_numeric($value) === false) {
            return '{field} must be a number';
        }
        if (isset($rules['enum']) && in_array($value, $rules['enum']) === false) {
            return '{field} must be one of '.implode(', ', $rules['enum']);
        }
        if (isset($rules['const']) && $value !== $rules['const']) {
            return '{field} must be '.$rules['const'];
        }
        if (isset($rules['minimum']) && $value < $rules['minimum']) {
            return '{field} must be greater than or equal to '.$rules['minimum'];
        }
        if (isset($rules['exclusiveMinimum']) && $value <= $rules['exclusiveMinimum']) {
            return '{field} must be greater than '.$rules['exclusiveMinimum'];
        }
        if (isset($rules['maximum']) && $value > $rules['maximum']) {
            return '{field} must be less than or equal to '.$rules['maximum'];
        }
        if (isset($rules['exclusiveMaximum']) && $value >= $rules['exclusiveMaximum']) {
            return '{field} must be less than '.$rules['exclusiveMaximum'];
        }
        if (isset($rules['multipleOf']) && ($value % $rules['multipleOf']) !== 0) {
            return '{field} must be a multiple of '.$rules['multipleOf'];
        }
        return true;
    }

    public static function validateObject(mixed $value, array $rules): bool | string
    {
        if (is_array($value) === false || array_is_list($value) === true) {
            return '{field} must be an object';
        }
        $keyCount = count(array_keys($value));
        if (isset($rules['minProperties']) && $keyCount < $rules['minProperties']) {
            return '{field} must have at least '.$rules['minProperties'].' properties';
        }
        if (isset($rules['maxProperties']) && $keyCount > $rules['maxProperties']) {
            return '{field} must have at most '.$rules['maxProperties'].' properties';
        }
        return true;
    }

    public function finalize(): array
    {
        $base = ['type' => $this->type];
        if ($this->readOnly) {
            $base['readOnly'] = true;
        }
        if ($this->writeOnly) {
            $base['writeOnly'] = true;
        }
        return array_merge(
            $base,
            $this->rules,
        );
    }
}
