<?php
/**
 * Input Validation Library
 * 
 * Provides reusable validation rules for form inputs.
 * 
 * @package BazarShop\Core\Validation
 */

namespace BazarShop\Core;

class Validator
{
    /**
     * Data to validate
     * 
     * @var array
     */
    private array $data;

    /**
     * Validation errors
     * 
     * @var array
     */
    private array $errors = [];

    /**
     * Custom error messages
     * 
     * @var array
     */
    private array $messages = [];

    /**
     * Constructor
     * 
     * @param array $data The data to validate
     * @param array $messages Custom error messages
     */
    public function __construct(array $data, array $messages = [])
    {
        $this->data = $data;
        $this->messages = $messages;
    }

    /**
     * Validate data against rules
     * 
     * @param array $rules Validation rules
     * @return bool True if valid, false otherwise
     */
    public function validate(array $rules): bool
    {
        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            
            foreach ($rulesArray as $rule) {
                $this->applyRule($field, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Apply a single validation rule
     * 
     * @param string $field Field name
     * @param string $rule Rule to apply
     */
    private function applyRule(string $field, string $rule): void
    {
        $value = $this->data[$field] ?? null;

        // Parse rule and parameters (e.g., min:3)
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $params = isset($ruleParts[1]) ? explode(',', $ruleParts[1]) : [];

        switch ($ruleName) {
            case 'required':
                if ($this->isRequired($value)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'min':
                $min = (int)$params[0];
                if (!empty($value) && strlen($value) < $min) {
                    $this->addError($field, $ruleName, ['min' => $min]);
                }
                break;

            case 'max':
                $max = (int)$params[0];
                if (!empty($value) && strlen($value) > $max) {
                    $this->addError($field, $ruleName, ['max' => $max]);
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'alpha':
                if (!empty($value) && !ctype_alpha($value)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'alphanumeric':
                if (!empty($value) && !ctype_alnum($value)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'url':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'in':
                $allowed = $params;
                if (!empty($value) && !in_array($value, $allowed, true)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'regex':
                $pattern = $params[0];
                if (!empty($value) && !preg_match($pattern, $value)) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'same':
                $otherField = $params[0];
                $otherValue = $this->data[$otherField] ?? null;
                if (!empty($value) && $value !== $otherValue) {
                    $this->addError($field, $ruleName, ['other' => $otherField]);
                }
                break;

            case 'file':
                if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
                    $this->addError($field, $ruleName);
                }
                break;

            case 'image':
                if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($_FILES[$field]['type'], $allowedTypes)) {
                        $this->addError($field, $ruleName);
                    }
                }
                break;
        }
    }

    /**
     * Check if a field is required but empty
     * 
     * @param mixed $value Field value
     * @return bool True if empty
     */
    private function isRequired($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && trim($value) === '') {
            return true;
        }

        if (is_array($value) && empty($value)) {
            return true;
        }

        return false;
    }

    /**
     * Add an error message
     * 
     * @param string $field Field name
     * @param string $rule Rule that failed
     * @param array $params Additional parameters for message
     */
    private function addError(string $field, string $rule, array $params = []): void
    {
        $message = $this->getMessage($field, $rule, $params);
        
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        
        $this->errors[$field][] = $message;
    }

    /**
     * Get error message for a rule
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array $params Parameters
     * @return string Error message
     */
    private function getMessage(string $field, string $rule, array $params): string
    {
        $customKey = "{$field}.{$rule}";
        
        if (isset($this->messages[$customKey])) {
            return $this->messages[$customKey];
        }

        // Default messages
        $defaults = [
            'required' => "The {$field} field is required.",
            'email' => "The {$field} must be a valid email address.",
            'min' => "The {$field} must be at least {$params['min']} characters.",
            'max' => "The {$field} must not exceed {$params['max']} characters.",
            'numeric' => "The {$field} must be numeric.",
            'alpha' => "The {$field} may only contain letters.",
            'alphanumeric' => "The {$field} may only contain letters and numbers.",
            'url' => "The {$field} must be a valid URL.",
            'in' => "The {$field} field contains an invalid value.",
            'regex' => "The {$field} field format is invalid.",
            'same' => "The {$field} field must match {$params['other']}.",
            'file' => "The {$field} must be a valid file.",
            'image' => "The {$field} must be a valid image (JPG, PNG, GIF, WebP).",
        ];

        return $defaults[$rule] ?? "The {$field} field is invalid.";
    }

    /**
     * Get all errors
     * 
     * @return array All validation errors
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error for a field
     * 
     * @param string $field Field name
     * @return string|null First error message or null
     */
    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Check if validation passed
     * 
     * @return bool True if no errors
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     * 
     * @return bool True if has errors
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }
}
