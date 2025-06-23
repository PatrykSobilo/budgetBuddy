<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Validator;
use Framework\Rules\{
  RequiredRule,
  EmailRule,
  MinRule,
  InRule,
  UrlRule,
  MatchRule,
  LengthMaxRule,
  NumericRule,
  DateFormatRule,
  UniqueCategoryRule
};
use Framework\Database;

class ValidatorService
{
  private Validator $validator;

  public function __construct()
  {
    $this->validator = new Validator();

    $this->validator->add('required', new RequiredRule());
    $this->validator->add('email', new EmailRule());
    $this->validator->add('min', new MinRule());
    $this->validator->add('match', new MatchRule());
    $this->validator->add('lengthMax', new LengthMaxRule());
    $this->validator->add('numeric', new NumericRule());
    $this->validator->add('dateFormat', new DateFormatRule());
  }

  public function validateRegister(array $formData)
  {
    $this->validator->validate($formData, [
      'email' => ['required', 'email'],
      'age' => ['required', 'min:18'],
      'password' => ['required'],
      'passwordConfirmation' => ['required', 'match:password'],
      'tos' => ['required']
    ]);
  }

  public function validateLogin(array $formData)
  {
    $this->validator->validate($formData, [
      'email' => ['required', 'email'],
      'password' => ['required']
    ]);
  }

  public function validateTransaction(array $formData)
  {
    $this->validator->validate($formData, [
      'description' => ['required', 'lengthMax:255'],
      'amount' => ['required', 'numeric'],
      'date' => ['required', 'dateFormat:Y-m-d']
    ]);
  }

  public function validateCategory(array $formData, string $type, int $userId, Database $db)
  {
    // Dodaj regułę unikalności dynamicznie dla danego typu
    $table = $type === 'income' ? 'incomes_category_assigned_to_users' : 'expenses_category_assigned_to_users';
    $this->validator->add('uniqueCategory', new UniqueCategoryRule($db, $table, $userId));
    $this->validator->validate($formData, [
      'name' => ['required', 'lengthMax:50', 'uniqueCategory']
    ]);
  }
}