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

    $_SESSION['expenseCategories'] = $this->db->query(
      "SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();

    $_SESSION['incomeCategories'] = $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();

    $_SESSION['paymentMethods'] = $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();
  }

  public function createUser(array $formData)
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

    $_SESSION['expenseCategories'] = $this->db->query(
      "SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $_SESSION['incomeCategories'] = $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $_SESSION['paymentMethods'] = $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
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

  public function getUserById(int $id)
  {
    return $this->db->query("SELECT email, age FROM users WHERE id = :id", [
      'id' => $id
    ])->find();
  }

  /**
   * Pobiera kategorie wydatków przypisane do użytkownika
   * @param int $userId
   * @return array
   */
  public function getExpenseCategories(int $userId): array
  {
    return $this->db->query(
      "SELECT id, name FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  /**
   * Pobiera kategorie przychodów przypisane do użytkownika
   * @param int $userId
   * @return array
   */
  public function getIncomeCategories(int $userId): array
  {
    return $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

  public function updateEmail(int $userId, string $email, $validatorService)
  {
    $validatorService->validateEmail(['email' => $email]);
    $this->isEmailTaken($email);
    $this->db->query(
      "UPDATE users SET email = :email WHERE id = :id",
      [
        'email' => $email,
        'id' => $userId
      ]
    );
  }

  public function updateAge(int $userId, string $age, $validatorService)
  {
    $validatorService->validateAge(['age' => $age]);
    $this->db->query(
      "UPDATE users SET age = :age WHERE id = :id",
      [
        'age' => $age,
        'id' => $userId
      ]
    );
  }

  public function updatePassword(int $userId, array $formData, $validatorService, $db = null)
  {
    $validatorService->validatePasswordChange($formData, $userId, $db ?? $this->db);
    $user = $this->db->query("SELECT password FROM users WHERE id = :id", ['id' => $userId])->find();
    if (!password_verify($formData['old_password'] ?? '', $user['password'] ?? '')) {
      throw new ValidationException(['old_password' => ['Current password is incorrect']]);
    }
    $newPassword = password_hash($formData['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);
    $this->db->query(
      "UPDATE users SET password = :password WHERE id = :id",
      [
        'password' => $newPassword,
        'id' => $userId
      ]
    );
  }

  public function getDb()
  {
    return $this->db;
  }
}
