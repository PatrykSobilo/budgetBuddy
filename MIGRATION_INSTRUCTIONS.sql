-- ============================================================
-- INSTRUKCJA MIGRACJI BAZY DANYCH - Category Limits Feature
-- ============================================================
-- 
-- KROK 1: Otwórz phpMyAdmin
-- KROK 2: Wybierz swoją bazę danych budgetBuddy
-- KROK 3: Przejdź do zakładki SQL
-- KROK 4: Skopiuj i wklej poniższe zapytanie
-- KROK 5: Kliknij "Wykonaj" (Execute/Go)
--
-- ============================================================

-- Dodaj kolumnę category_limit TYLKO do tabeli kategorii wydatków
ALTER TABLE expenses_category_assigned_to_users
ADD COLUMN category_limit DECIMAL(10,2) NULL DEFAULT NULL
COMMENT 'Monthly spending limit for this category (optional)';

-- ============================================================
-- WERYFIKACJA: Sprawdź czy kolumna została dodana
-- ============================================================

-- Sprawdź strukturę tabeli expenses_category_assigned_to_users
DESCRIBE expenses_category_assigned_to_users;

-- ============================================================
-- GOTOWE! 
-- Po wykonaniu tego zapytania odśwież stronę w przeglądarce.
-- Możesz teraz ustawiać limity dla kategorii WYDATKÓW w Settings.
-- Kategorie przychodów NIE mają limitów (nie ma to sensu).
-- ============================================================
