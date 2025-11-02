<?php

declare(strict_types=1);

use Framework\{TemplateEngine, Database, Container};
use App\Config\Paths;
use App\Services\{
  ValidatorService,
  UserService,
  TransactionService,
  ReceiptService,
  SettingsService,
  DatePeriodService,
  ViewHelperService,
  TransactionSearchService,
  ResponseService,
  FlashService,
  SessionService,
  Request,
  AuthService,
  BudgetCalculatorService
};
use App\Repositories\{TransactionRepository, CategoryRepository, UserRepository};

return [
  TemplateEngine::class => fn () => new TemplateEngine(Paths::VIEW),
  ValidatorService::class => fn () => new ValidatorService(),
  Database::class => fn () => new Database($_ENV['DB_DRIVER'], [
    'host' => $_ENV['DB_HOST'],
    'port' => $_ENV['DB_PORT'],
    'dbname' => $_ENV['DB_NAME']
  ], $_ENV['DB_USER'], $_ENV['DB_PASS']),
  UserService::class => function (Container $container) {
    $db = $container->get(Database::class);
    $userRepository = $container->get(UserRepository::class);
    $categoryRepository = $container->get(CategoryRepository::class);

    return new UserService($db, $userRepository, $categoryRepository);
  },
  BudgetCalculatorService::class => function (Container $container) {
    $db = $container->get(Database::class);
    $categoryRepository = $container->get(CategoryRepository::class);
    return new BudgetCalculatorService($db, $categoryRepository);
  },
  CategoryRepository::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new CategoryRepository($db);
  },
  UserRepository::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new UserRepository($db);
  },
  TransactionRepository::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new TransactionRepository($db);
  },
  TransactionService::class => function (Container $container) {
    $db = $container->get(Database::class);
    $budgetCalculator = $container->get(BudgetCalculatorService::class);
    $transactionRepository = $container->get(TransactionRepository::class);

    return new TransactionService($db, $budgetCalculator, $transactionRepository);
  },
  ReceiptService::class => function (Container $container) {
    $db = $container->get(Database::class);

    return new ReceiptService($db);
  },
  SettingsService::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new \App\Services\SettingsService($db);
  },
  DatePeriodService::class => fn () => new DatePeriodService(),
  ViewHelperService::class => function (Container $container) {
    $session = $container->get(SessionService::class);
    return new ViewHelperService($session);
  },
  TransactionSearchService::class => function (Container $container) {
    $viewHelper = $container->get(ViewHelperService::class);
    return new TransactionSearchService($viewHelper);
  },
  ResponseService::class => fn () => new ResponseService(),
  FlashService::class => fn () => new FlashService(),
  SessionService::class => fn () => new SessionService(),
  Request::class => fn () => new Request(),
  AuthService::class => function (Container $container) {
    $session = $container->get(SessionService::class);
    return new AuthService($session);
  }
];