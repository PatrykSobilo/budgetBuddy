<?php

declare(strict_types=1);

namespace App\Repositories;

use Framework\Database;

class UserRepository
{
  public function __construct(private Database $db) {}

  public function findByEmail(string $email): ?array
  {
    return $this->db->query(
      "SELECT * FROM users WHERE email = :email",
      ['email' => $email]
    )->find();
  }

  public function emailExists(string $email): bool
  {
    $count = $this->db->query(
      "SELECT COUNT(*) as count FROM users WHERE email = :email",
      ['email' => $email]
    )->find();

    return $count && $count['count'] > 0;
  }

  public function create(array $userData): int
  {
    $this->db->query(
      "INSERT INTO users(email, password, age) VALUES(:email, :password, :age)",
      [
        'email' => $userData['email'],
        'password' => $userData['password'],
        'age' => $userData['age']
      ]
    );

    return (int)$this->db->id();
  }

  public function findById(int $userId): ?array
  {
    return $this->db->query(
      "SELECT * FROM users WHERE id = :id",
      ['id' => $userId]
    )->find();
  }

  public function updatePassword(int $userId, string $hashedPassword): void
  {
    $this->db->query(
      "UPDATE users SET password = :password WHERE id = :id",
      ['password' => $hashedPassword, 'id' => $userId]
    );
  }

  public function updateEmail(int $userId, string $email): void
  {
    $this->db->query(
      "UPDATE users SET email = :email WHERE id = :id",
      ['email' => $email, 'id' => $userId]
    );
  }

  public function delete(int $userId): void
  {
    $this->db->query(
      "DELETE FROM users WHERE id = :id",
      ['id' => $userId]
    );
  }
}
