CREATE TABLE IF NOT EXISTS users(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  age tinyint(3) unsigned NOT NULL,
  PRIMARY KEY(id),
  UNIQUE KEY(email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS incomes_category_default(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB;

INSERT INTO incomes_category_default (name) VALUES
  ('Salary'),
  ('Gift')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE IF NOT EXISTS expenses_category_default(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB;

INSERT INTO expenses_category_default (name) VALUES
  ('Groceries'),
  ('Transport')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE IF NOT EXISTS payment_methods_default(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(50) NOT NULL,
  PRIMARY KEY(id)
) ENGINE=InnoDB;

INSERT INTO payment_methods_default (name) VALUES
  ('Card'),
  ('Cash')
ON DUPLICATE KEY UPDATE name = VALUES(name);

CREATE TABLE IF NOT EXISTS incomes_category_assigned_to_users(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS expenses_category_assigned_to_users(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  category_limit decimal(10,2) DEFAULT NULL COMMENT 'Monthly spending limit for this category (optional)',
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payment_methods_assigned_to_users(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  name varchar(50) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS incomes(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  income_category_assigned_to_user_id int(11) unsigned NOT NULL,
  amount decimal(8,2) NOT NULL,
  date_of_income datetime NOT NULL,
  income_comment varchar(100) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id),
  FOREIGN KEY(income_category_assigned_to_user_id) REFERENCES incomes_category_assigned_to_users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS expenses(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  expense_category_assigned_to_user_id int(11) unsigned NOT NULL,
  payment_method_assigned_to_user_id int(11) unsigned NOT NULL,
  amount decimal(8,2) NOT NULL,
  date_of_expense datetime NOT NULL,
  expense_comment varchar(100) NOT NULL,
  PRIMARY KEY(id),
  FOREIGN KEY(user_id) REFERENCES users(id),
  FOREIGN KEY(expense_category_assigned_to_user_id) REFERENCES expenses_category_assigned_to_users(id),
  FOREIGN KEY(payment_method_assigned_to_user_id) REFERENCES payment_methods_assigned_to_users(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_insights(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  insight_type varchar(50) NOT NULL COMMENT 'Type of insight: spending_insights, alerts, tips',
  content text NOT NULL COMMENT 'AI-generated insight content (JSON format)',
  generated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  valid_until datetime NOT NULL COMMENT 'When this insight expires (typically 24h)',
  PRIMARY KEY(id),
  KEY idx_user_type_valid (user_id, insight_type, valid_until),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ai_chat_sessions(
  id int(11) unsigned NOT NULL AUTO_INCREMENT,
  user_id int(11) unsigned NOT NULL,
  role varchar(10) NOT NULL COMMENT 'user or model',
  message text NOT NULL,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(id),
  KEY idx_user_created (user_id, created_at),
  FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;










