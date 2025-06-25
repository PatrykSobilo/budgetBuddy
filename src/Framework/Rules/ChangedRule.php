<?php

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;

class ChangedRule implements RuleInterface
{
    public function validate(array $data, string $field, array $params): bool
    {
        $originalKey = 'original_' . $field;
        if (!array_key_exists($originalKey, $data)) {
            return true;
        }
        return $data[$originalKey] != $data[$field];
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return 'No changes made. Close or change any value';
    }
}
