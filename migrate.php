<?php

declare(strict_types=1);

// Skrypt migracji bazy danych - AI Monthly Summaries
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

    // Sprawdź czy tabela ai_monthly_summaries już istnieje
    $checkTable = $db->query(
        "SELECT COUNT(*) as cnt 
         FROM INFORMATION_SCHEMA.TABLES 
         WHERE TABLE_SCHEMA = :dbname 
         AND TABLE_NAME = 'ai_monthly_summaries'",
        ['dbname' => $_ENV['DB_NAME']]
    )->find();

    if ($checkTable['cnt'] > 0) {
        echo "⚠ Tabela 'ai_monthly_summaries' już istnieje\n";
        
        // Sprawdź czy nowe kolumny istnieją
        $checkColumns = $db->query(
            "SELECT COUNT(*) as cnt 
             FROM INFORMATION_SCHEMA.COLUMNS 
             WHERE TABLE_SCHEMA = :dbname 
             AND TABLE_NAME = 'ai_monthly_summaries' 
             AND COLUMN_NAME IN ('ai_summary', 'key_issues', 'recommendations', 'is_finalized')",
            ['dbname' => $_ENV['DB_NAME']]
        )->find();
        
        if ($checkColumns['cnt'] < 4) {
            echo "→ Dodawanie nowych kolumn do tabeli 'ai_monthly_summaries'...\n";
            $db->query(
                "ALTER TABLE ai_monthly_summaries
                 ADD COLUMN IF NOT EXISTS ai_summary TEXT COMMENT 'AI-generated monthly summary with advice',
                 ADD COLUMN IF NOT EXISTS key_issues TEXT COMMENT 'Main problems identified (e.g., Too much FastFood spending)',
                 ADD COLUMN IF NOT EXISTS recommendations TEXT COMMENT 'AI recommendations for next month',
                 ADD COLUMN IF NOT EXISTS is_finalized TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 when month is completed and AI summary generated'"
            );
            echo "✓ Kolumny dodane pomyślnie!\n";
        } else {
            echo "✓ Wszystkie kolumny już istnieją\n";
        }
    } else {
        echo "→ Tworzenie tabeli 'ai_monthly_summaries'...\n";
        $db->query(
            "CREATE TABLE ai_monthly_summaries(
              id int(11) unsigned NOT NULL AUTO_INCREMENT,
              user_id int(11) unsigned NOT NULL,
              year int(4) NOT NULL,
              month int(2) NOT NULL,
              total_income decimal(10,2) NOT NULL DEFAULT 0,
              total_expenses decimal(10,2) NOT NULL DEFAULT 0,
              transaction_count int(11) NOT NULL DEFAULT 0,
              top_expense_category varchar(50),
              top_expense_amount decimal(10,2),
              ai_summary TEXT COMMENT 'AI-generated monthly summary with advice',
              key_issues TEXT COMMENT 'Main problems identified (e.g., Too much FastFood spending)',
              recommendations TEXT COMMENT 'AI recommendations for next month',
              is_finalized TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 when month is completed and AI summary generated',
              created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
              updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY(id),
              UNIQUE KEY unique_user_month (user_id, year, month),
              FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB"
        );
        echo "✓ Tabela utworzona pomyślnie!\n";
    }

    echo "\n✓✓✓ MIGRACJA ZAKOŃCZONA POMYŚLNIE! ✓✓✓\n";
    echo "\nAI Advisor będzie teraz generować pełne podsumowania miesięczne z poradami!\n";
    echo "Tabela będzie automatycznie wypełniana przy generowaniu insights.\n";

} catch (Exception $e) {
    echo "✗ BŁĄD: " . $e->getMessage() . "\n";
    echo "\nJeśli problem dotyczy połączenia z bazą danych:\n";
    echo "1. Upewnij się, że XAMPP/MySQL jest uruchomiony\n";
    echo "2. Sprawdź plik .env (DB_HOST, DB_USER, DB_PASS, DB_NAME)\n";
    exit(1);
}
