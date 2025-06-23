<?php

declare(strict_types=1);

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;
use Framework\Database;

class UniqueCategoryRule implements RuleInterface
{
    public function __construct(private Database $db, private string $table, private int $userId) {}

    public function validate(array $data, string $field, array $params): bool
    {
        $name = $data[$field] ?? '';
        $result = $this->db->query(
            "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id AND name = :name",
            [
                'user_id' => $this->userId,
                'name' => $name
            ]
        )->count();
        return $result == 0;
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return 'This category already exists.';
    }
}
