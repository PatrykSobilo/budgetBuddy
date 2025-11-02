<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;

class UserService
{
  public function __construct(private Database $db) {}

  public function getPaymentMethods(int $userId): array
  {
    return $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
  }

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

  public function login(array $formData): array
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

    $expenseCategories = $this->db->query(
      "SELECT id, name, category_limit FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();

    $incomeCategories = $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();

    $paymentMethods = $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $user['id']]
    )->findAll();

    return [
      'userId' => $user['id'],
      'expenseCategories' => $expenseCategories,
      'incomeCategories' => $incomeCategories,
      'paymentMethods' => $paymentMethods
    ];
  }

  public function createUser(array $formData): array
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

    $expenseCategories = $this->db->query(
      "SELECT id, name, category_limit FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $incomeCategories = $this->db->query(
      "SELECT id, name FROM incomes_category_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();
    $paymentMethodsData = $this->db->query(
      "SELECT id, name FROM payment_methods_assigned_to_users WHERE user_id = :user_id",
      ['user_id' => $userId]
    )->findAll();

    return [
      'userId' => $userId,
      'expenseCategories' => $expenseCategories,
      'incomeCategories' => $incomeCategories,
      'paymentMethods' => $paymentMethodsData
    ];
  }

  public function logout(): void
  {
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
      "SELECT id, name, category_limit FROM expenses_category_assigned_to_users WHERE user_id = :user_id",
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

  /**
   * Usuwa użytkownika i wszystkie powiązane z nim dane z bazy
   */
  public function deleteUserAndData(int $userId): void
  {
    // Usuń wydatki
    $this->db->query("DELETE FROM expenses WHERE user_id = :user_id", ['user_id' => $userId]);
    // Usuń przychody
    $this->db->query("DELETE FROM incomes WHERE user_id = :user_id", ['user_id' => $userId]);
    // Usuń kategorie wydatków
    $this->db->query("DELETE FROM expenses_category_assigned_to_users WHERE user_id = :user_id", ['user_id' => $userId]);
    // Usuń kategorie przychodów
    $this->db->query("DELETE FROM incomes_category_assigned_to_users WHERE user_id = :user_id", ['user_id' => $userId]);
    // Usuń metody płatności
    $this->db->query("DELETE FROM payment_methods_assigned_to_users WHERE user_id = :user_id", ['user_id' => $userId]);
    // Usuń użytkownika
    $this->db->query("DELETE FROM users WHERE id = :user_id", ['user_id' => $userId]);
  }

  public function getDb()
  {
    return $this->db;
  }
}
