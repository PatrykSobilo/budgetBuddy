<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
  public function __construct(private Database $db) {}

  public function isEmailTaken(string $email)
  {
    $emailCount = $this->db->query(
      "SELECT COUNT(*) FROM users WHERE email = :email",
      [
        'email' => $email
      ]
    )->count();

    if ($emailCount > 0) {
      throw new ValidationException(['email' => ['Email taken']]);
    }
  }

  public function create(array $formData)
  {
    $password = password_hash($formData['password'], PASSWORD_BCRYPT, ['cost' => 12]);

    $this->db->query(
      "INSERT INTO users(email,password,age)
      VALUES(:email, :password, :age)",
      [
        'email' => $formData['email'],
        'password' => $password,
        'age' => $formData['age'],
      ]
    );

    session_regenerate_id();

    $_SESSION['user'] = $this->db->id();
    $userId = $this->db->id();

    $incomesCategories = $this->db->query("SELECT name FROM incomes_category_default")->findAll();
    foreach ($incomesCategories as $category) {
      $this->db->query(
        "INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
        ['user_id' => $userId, 'name' => $category['name']]
      );
    }

    $expensesCategories = $this->db->query("SELECT name FROM expenses_category_default")->findAll();
    foreach ($expensesCategories as $category) {
      $this->db->query(
        "INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
        ['user_id' => $userId, 'name' => $category['name']]
      );
    }

    $paymentMethods = $this->db->query("SELECT name FROM payment_methods_default")->findAll();
    foreach ($paymentMethods as $method) {
      $this->db->query(
        "INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)",
        ['user_id' => $userId, 'name' => $method['name']]
      );
    }
  }

  public function login(array $formData)
  {
    $user = $this->db->query("SELECT * FROM users WHERE email = :email", [
      'email' => $formData['email']
    ])->find();

    $passwordsMatch = password_verify(
      $formData['password'],
      $user['password'] ?? ''
    );

    if (!$user || !$passwordsMatch) {
      throw new ValidationException(['password' => ['Invalid credentials']]);
    }

    session_regenerate_id();
    $_SESSION['user'] = $user['id'];

    $incomesCategories = $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();
    $_SESSION['incomes_categories'] = $incomesCategories;

    $expensesCategories = $this->db->query(
      "SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();
    $_SESSION['expenses_categories'] = $expensesCategories;

    $paymentMethods = $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();
    $_SESSION['payment_methods'] = $paymentMethods;
  }

  public function logout()
  {
    unset($_SESSION['user']);
    session_destroy();

    session_regenerate_id();
    $params = session_get_cookie_params();
    setcookie(
      'PHPSESSID',
      '',
      time() - 3600,
      $params['path'],
      $params['domain'],
      $params['secure'],
      $params['httponly']
    );
  }
}
