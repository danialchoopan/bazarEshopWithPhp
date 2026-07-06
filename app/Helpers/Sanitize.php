<?php

namespace App\Helpers;

class Sanitize
{
    /**
     * Escape HTML output — prevents XSS
     */
    public static function e(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize string input (trim + strip tags)
     */
    public static function string(?string $value): string
    {
        return trim(strip_tags($value ?? ''));
    }

    /**
     * Sanitize integer input
     */
    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize email
     */
    public static function email(?string $value): string
    {
        return filter_var(trim($value ?? ''), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validate email format
     */
    public static function isValidEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Sanitize phone number (keep digits only)
     */
    public static function phone(?string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value ?? '');
    }

    /**
     * Validate and sanitize price (numeric, non-negative)
     */
    public static function price(mixed $value): float
    {
        $val = (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        return max(0, $val);
    }

    /**
     * Sanitize filename (remove dangerous characters)
     */
    public static function filename(?string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9._-]/', '', basename($value ?? ''));
    }

    /**
     * Generate a random filename for uploads
     */
    public static function randomFilename(string $originalName): string
    {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        return bin2hex(random_bytes(16)) . '.' . strtolower($extension);
    }

    /**
     * Validate input against rules
     * @param array $data Key => value pairs
     * @param array $rules Key => rule string (e.g., 'required|email')
     * @return array ['valid' => bool, 'errors' => []]
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $ruleName = $rule;

                if (str_starts_with($rule, 'min:')) {
                    $min = (int) substr($rule, 4);
                    if (strlen((string) $value) < $min) {
                        $errors[$field] = "حداقل {$min} کاراکتر وارد کنید";
                    }
                } elseif (str_starts_with($rule, 'max:')) {
                    $max = (int) substr($rule, 4);
                    if (strlen((string) $value) > $max) {
                        $errors[$field] = "حداکثر {$max} کاراکتر وارد کنید";
                    }
                } elseif ($ruleName === 'required') {
                    if ($value === null || $value === '' || $value === []) {
                        $errors[$field] = "این فیلد الزامی است";
                    }
                } elseif ($ruleName === 'email') {
                    if (!empty($value) && !self::isValidEmail((string) $value)) {
                        $errors[$field] = "ایمیل معتبر نیست";
                    }
                } elseif ($ruleName === 'numeric') {
                    if (!empty($value) && !is_numeric($value)) {
                        $errors[$field] = "باید عدد باشد";
                    }
                } elseif ($ruleName === 'phone') {
                    if (!empty($value) && !preg_match('/^[0-9]{10,15}$/', (string) $value)) {
                        $errors[$field] = "شماره تلفن معتبر نیست";
                    }
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
