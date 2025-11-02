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
  TransactionSearchService
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
  TransactionService::class => function (Container $container) {
    $db = $container->get(Database::class);

    return new TransactionService($db);
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
  ViewHelperService::class => fn () => new ViewHelperService(),
  TransactionSearchService::class => function (Container $container) {
    $viewHelper = $container->get(ViewHelperService::class);
    return new TransactionSearchService($viewHelper);
  }
];