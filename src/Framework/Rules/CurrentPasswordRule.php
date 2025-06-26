<?php

namespace Framework\Rules;

use Framework\Contracts\RuleInterface;
use Framework\Database;

class CurrentPasswordRule implements RuleInterface
{
    private Database $db;
    private int $userId;

    public function __construct(Database $db, int $userId)
    {
        $this->db = $db;
        $this->userId = $userId;
    }

    public function validate(array $data, string $field, array $params): bool
    {
        $user = $this->db->query("SELECT password FROM users WHERE id = :id", ['id' => $this->userId])->find();
        if (!$user) return false;
        return password_verify($data[$field] ?? '', $user['password'] ?? '');
    }

    public function getMessage(array $data, string $field, array $params): string
    {
        return "Current password is incorrect.";
    }
}
