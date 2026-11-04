-- =====================================================
-- Event Discovery and Management Platform
-- Database schema
-- Version: 0.1 
-- Date: 3 November 2026
-- =====================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS event_platform

  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE event_platform;

-- Development only: drop in reverse dependency order
DROP TABLE IF EXISTS event_stats_daily;
DROP TABLE IF EXISTS event_change_log;
DROP TABLE IF EXISTS event_approved_versions;
DROP TABLE IF EXISTS moderation_actions;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS calendar_entries;
DROP TABLE IF EXISTS reminders;
DROP TABLE IF EXISTS saved_events;
DROP TABLE IF EXISTS user_interests;
DROP TABLE IF EXISTS event_images;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS event_series;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS organiser_social_links;
DROP TABLE IF EXISTS organiser_profiles;
DROP TABLE IF EXISTS users; 
DROP TABLE IF EXISTS app_settings;

--------USERS TABLE -----
CREATE TABLE users (
  user_id INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  email VARCHAR(100) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('user', 'organiser', 'admin') NOT NULL DEFAULT 'attendee',
  account_status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (user_id),
 UNIQUE KEY uq_users_email (email)
 )ENGINE= InnoDB;

--------ORGANISER PROFILES TABLE -----
CREATE TABLE organiser_profiles (
  organiser_id INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  user_id INT UNSIGNED NOT NULL,
  org_name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  contact_email VARCHAR(255) NULL,
  contact_phone VARCHAR(30) NULL,
  is_trusted TINYINT(1) NOT NULL DEFAULT 0, --AR-09(Could have)
 PRIMARY KEY (organiser_id),
 UNIQUE KEY uq_organiser_user (user_id),
  CONSTRAINT fk_organiser_user FOREIGN KEY (user_id) REFERENCES users(user_id)
   ON DELETE CASCADE

)ENGINE = InnoDB;

SET FOREIGN_KEY_CHECKS = 1;


-- ---------- ACCOUNTS ----------


-- categories
CREATE TABLE categories(
 category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 name VARCHAR(100) NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 PRIMARY KEY(category_id),
 UNIQUE KEY uq_categories_name(name),
)ENGINE=InnoDB;

-- organiser_profiles
CREATE TABLE organiser_social_links (
    link_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    organiser_id INT UNSIGNED NOT NULL,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    PRIMARY KEY (link_id),
    KEY idx_social_organiser_id (organiser_id),
    CONSTRAINT fk_social_organiser FOREIGN KEY (organiser_id) REFERENCES organiser_profiles(organiser_id)
        ON DELETE CASCADE
) 

---------USER INTERESTS TABLE -----
CREATE TABLE user_interests (
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY(user_id, category_id)
    KEY idx_interests_category(category_id),
    CONSTRAINT fk_interest_user
    FOREGIN KEY(user_id) REFERENCES users(user_id)
    ON DELETE CASCADE,
    CONSTRAINT fk)interest_category
    FOREGIN KEY(category_id)REFERENCES categories(category_id)
    ON DELETE CASCADE
)ENGINE=InnoDB;

---------EVENT SERIES TABLE -----
CREATE TABLE event_series (
    series_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    organiser_id INT UNSIGNED NOT NULL,
    recurrence_type ENUM('daily', 'weekly', 'monthly') NOT NULL,
    start_date DATE NOT NULL ,
    end_time DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('active','ended', 'archived') NOT NULL DEFAULT 'active',
    generated_until DATE NOT NULL,
    last_confirmed_at DATETIME NOT NULL ,
    next_prompt_at DATETIME NOT NULL,
    prompt_sent_at DATETIME NULL,
    created_at DATETIME NOT NULL CURRENT_TIMESTAMP,
    PRIMARY KEY(series_id),
    KEY idx_series_organiser(organiser_id),
    KEY idx_series_prompt(status),
    CONSTRAINT fk_series_organiser
    FOREIGN KEY(organiser_id) REFERENCES organiser_profiles(organiser_id)
    ON DELETE CASCADE,
    CONSTRAINT chk_series_dates CHECK(end_date IS NULL OR end_date >=start_date)
)ENGINE=InnoDB;
    -- ---------- SEED DATA ----------

-- TODO: categories

-- ---------- CHECKS ----------

SHOW TABLES;