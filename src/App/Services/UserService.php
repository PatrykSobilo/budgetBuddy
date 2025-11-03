<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;
use Framework\Exceptions\ValidationException;
use App\Repositories\UserRepository;
use App\Repositories\CategoryRepository;

class UserService
{
  public function __construct(
    private Database $db,
    private UserRepository $userRepository,
    private CategoryRepository $categoryRepository
  ) {}

  public function getPaymentMethods(int $userId): array
  {
    return $this->categoryRepository->getPaymentMethods($userId);
  }

  public function isEmailTaken(string $email)
  {
    if ($this->userRepository->emailExists($email)) {
      throw new ValidationException(['email' => ['Email taken']]);
    }
  }

  public function login(array $formData): array
  {
    $user = $this->userRepository->findByEmail($formData['email']);

    $passwordsMatch = password_verify(
      $formData['password'],
      $user['password'] ?? ''
    );

    if (!$user || !$passwordsMatch) {
      throw new ValidationException(['password' => ['Invalid credentials']]);
    }

    $expenseCategories = $this->categoryRepository->getExpenseCategories($user['id']);
    $incomeCategories = $this->categoryRepository->getIncomeCategories($user['id']);
    $paymentMethods = $this->categoryRepository->getPaymentMethods($user['id']);

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

    $userId = $this->userRepository->create([
      'email' => $formData['email'],
      'password' => $password,
      'age' => $formData['age'],
    ]);

    $incomesCategories = $this->categoryRepository->getDefaultIncomeCategories();
    foreach ($incomesCategories as $category) {
      $this->categoryRepository->createIncomeCategory($userId, $category['name']);
    }

    $expensesCategories = $this->categoryRepository->getDefaultExpenseCategories();
    foreach ($expensesCategories as $category) {
      $this->categoryRepository->createExpenseCategory($userId, $category['name']);
    }

    $paymentMethods = $this->categoryRepository->getDefaultPaymentMethods();
    foreach ($paymentMethods as $method) {
      $this->categoryRepository->createPaymentMethod($userId, $method['name']);
    }

    $expenseCategories = $this->categoryRepository->getExpenseCategories($userId);
    $incomeCategories = $this->categoryRepository->getIncomeCategories($userId);
    $paymentMethodsData = $this->categoryRepository->getPaymentMethods($userId);

    return [
      'userId' => $userId,
      'expenseCategories' => $expenseCategories,
      'incomeCategories' => $incomeCategories,
      'paymentMethods' => $paymentMethodsData
    ];
  }

  public function logout(): void
  {
    $params = session_get_cookie_params();
    
    session_destroy();
    
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
    return $this->userRepository->findById($id);
  }

  /**
   * Pobiera kategorie wydatków przypisane do użytkownika
   * @param int $userId
   * @return array
   */
  public function getExpenseCategories(int $userId): array
  {
    return $this->categoryRepository->getExpenseCategories($userId);
  }

  /**
   * Pobiera kategorie przychodów przypisane do użytkownika
   * @param int $userId
   * @return array
   */
  public function getIncomeCategories(int $userId): array
  {
    return $this->categoryRepository->getIncomeCategories($userId);
  }

  public function updateEmail(int $userId, string $email, $validatorService)
  {
    $validatorService->validateEmail(['email' => $email]);
    $this->isEmailTaken($email);
    $this->userRepository->updateEmail($userId, $email);
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
    $user = $this->userRepository->findById($userId);
    if (!password_verify($formData['old_password'] ?? '', $user['password'] ?? '')) {
      throw new ValidationException(['old_password' => ['Current password is incorrect']]);
    }
    $newPassword = password_hash($formData['new_password'], PASSWORD_BCRYPT, ['cost' => 12]);
    $this->userRepository->updatePassword($userId, $newPassword);
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
    $this->userRepository->delete($userId);
  }

  public function getDb()
  {
    return $this->db;
  }
}
