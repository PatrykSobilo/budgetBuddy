-- Migration: Add category_limit column to expense categories table
-- Date: 2025-10-22
-- Description: Add optional monthly spending limit for expense categories
-- NOTE: Income categories do NOT have limits (it doesn't make sense for budgeting)

-- Add category_limit to expenses categories (assigned to users)
ALTER TABLE expenses_category_assigned_to_users
ADD COLUMN category_limit DECIMAL(10,2) NULL DEFAULT NULL
COMMENT 'Monthly spending limit for this category (optional)';

-- Verify the changes
SELECT 'expenses_category_assigned_to_users' AS table_name, 
       COLUMN_NAME, 
       DATA_TYPE, 
       IS_NULLABLE, 
       COLUMN_DEFAULT,
       COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'expenses_category_assigned_to_users'
  AND COLUMN_NAME = 'category_limit'
  AND TABLE_SCHEMA = DATABASE();
