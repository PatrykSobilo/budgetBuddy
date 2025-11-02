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

    return new UserService($db);
  },
  BudgetCalculatorService::class => function (Container $container) {
    $db = $container->get(Database::class);
    return new BudgetCalculatorService($db);
  },
  TransactionService::class => function (Container $container) {
    $db = $container->get(Database::class);
    $budgetCalculator = $container->get(BudgetCalculatorService::class);

    return new TransactionService($db, $budgetCalculator);
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