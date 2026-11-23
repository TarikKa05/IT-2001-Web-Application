<?php
require_once __DIR__ . '/exceptions.php';

class Validator
{
    public static function required($data, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim((string) $data[$field]) === '') {
                throw new ValidationException("Field '$field' is required.");
            }
        }
    }

    public static function length($value, $field, $min, $max)
    {
        $length = mb_strlen(trim((string) $value));
        if ($length < $min || $length > $max) {
            throw new ValidationException("Field '$field' must be between $min and $max characters long.");
        }
    }

    public static function integer($value, $field, $min = null, $max = null)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            throw new ValidationException("Field '$field' must be a whole number.");
        }
        if ($min !== null && $value < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
        if ($max !== null && $value > $max) {
            throw new ValidationException("Field '$field' must be at most $max.");
        }
    }

    public static function number($value, $field, $min = null, $max = null)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            throw new ValidationException("Field '$field' must be a number.");
        }
        if ($min !== null && $value < $min) {
            throw new ValidationException("Field '$field' must be at least $min.");
        }
        if ($max !== null && $value > $max) {
            throw new ValidationException("Field '$field' must be at most $max.");
        }
    }

    public static function one_of($value, $field, $allowed)
    {
        if (!in_array($value, $allowed, true)) {
            throw new ValidationException("Field '$field' must be one of: " . implode(', ', $allowed) . ".");
        }
    }

    public static function email($value, $field = 'email')
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException("Field '$field' must be a valid e-mail address.");
        }
    }

    public static function phone($value, $field = 'phone_number')
    {
        if (!preg_match('/^\+?[0-9]{6,20}$/', (string) $value)) {
            throw new ValidationException("Field '$field' must contain only digits (6 to 20).");
        }
    }

    public static function date($value, $field)
    {
        $date = DateTime::createFromFormat('Y-m-d', (string) $value);
        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new ValidationException("Field '$field' must be a date in the format YYYY-MM-DD.");
        }
    }

    public static function datetime($value, $field)
    {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', (string) $value);
        if (!$date || $date->format('Y-m-d H:i:s') !== $value) {
            throw new ValidationException("Field '$field' must be a date and time in the format YYYY-MM-DD HH:MM:SS.");
        }
    }

    public static function password($value, $field = 'password')
    {
        $value = (string) $value;
        if (mb_strlen($value) < 8 || !preg_match('/[A-Za-z]/', $value) || !preg_match('/[0-9]/', $value)) {
            throw new ValidationException("Field '$field' must have at least 8 characters, including a letter and a number.");
        }
    }

    public static function no_html($value, $field)
    {
        if (preg_match('/[<>]/', (string) $value)) {
            throw new ValidationException("Field '$field' must not contain HTML.");
        }
    }
}
