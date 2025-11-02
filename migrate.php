<?php

declare(strict_types=1);

// Skrypt migracji bazy danych - Category Limits
// Uruchom: php migrate.php

require __DIR__ . '/vendor/autoload.php';

use Framework\Database;

// Załaduj zmienne środowiskowe
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

try {
    $db = new Database(
        $_ENV['DB_DRIVER'],
        [
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME']
        ],
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );

    echo "✓ Połączono z bazą danych\n\n";

    // Sprawdź czy kolumna już istnieje w expenses_category_assigned_to_users
    $checkExpenses = $db->query(
        "SELECT COUNT(*) as cnt 
         FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = :dbname 
         AND TABLE_NAME = 'expenses_category_assigned_to_users' 
         AND COLUMN_NAME = 'category_limit'",
        ['dbname' => $_ENV['DB_NAME']]
    )->find();

    if ($checkExpenses['cnt'] > 0) {
        echo "⚠ Kolumna 'category_limit' już istnieje w tabeli 'expenses_category_assigned_to_users'\n";
    } else {
        echo "→ Dodawanie kolumny 'category_limit' do tabeli 'expenses_category_assigned_to_users'...\n";
        $db->query(
            "ALTER TABLE expenses_category_assigned_to_users
             ADD COLUMN category_limit DECIMAL(10,2) NULL DEFAULT NULL
             COMMENT 'Monthly spending limit for this category (optional)'"
        );
        echo "✓ Kolumna dodana pomyślnie!\n";
    }

    // Usuń kolumnę category_limit z incomes_category_assigned_to_users (jeśli istnieje)
    $checkIncomes = $db->query(
        "SELECT COUNT(*) as cnt 
         FROM INFORMATION_SCHEMA.COLUMNS 
         WHERE TABLE_SCHEMA = :dbname 
         AND TABLE_NAME = 'incomes_category_assigned_to_users' 
         AND COLUMN_NAME = 'category_limit'",
        ['dbname' => $_ENV['DB_NAME']]
    )->find();

    if ($checkIncomes['cnt'] > 0) {
        echo "→ Usuwanie kolumny 'category_limit' z tabeli 'incomes_category_assigned_to_users'...\n";
        $db->query("ALTER TABLE incomes_category_assigned_to_users DROP COLUMN category_limit");
        echo "✓ Kolumna usunięta pomyślnie!\n";
    } else {
        echo "⚠ Kolumna 'category_limit' nie istnieje w tabeli 'incomes_category_assigned_to_users' (OK)\n";
    }

    echo "\n✓✓✓ MIGRACJA ZAKOŃCZONA POMYŚLNIE! ✓✓✓\n";
    echo "\nMożesz teraz używać funkcji Category Limits w aplikacji!\n";
    echo "Przejdź do: http://localhost:8000/settings\n";

} catch (Exception $e) {
    echo "✗ BŁĄD: " . $e->getMessage() . "\n";
    echo "\nJeśli problem dotyczy połączenia z bazą danych:\n";
    echo "1. Upewnij się, że XAMPP/MySQL jest uruchomiony\n";
    echo "2. Sprawdź plik .env (DB_HOST, DB_USER, DB_PASS, DB_NAME)\n";
    exit(1);
}
