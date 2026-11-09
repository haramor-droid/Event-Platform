-- =====================================================
-- Event Discovery and Management Platform
-- Database schema
-- Version: 0.2
-- Date: 4 November 2026
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
SET FOREIGN_KEY_CHECKS=1;

-- Users Table 
CREATE TABLE users (
  user_id INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  email VARCHAR(100) NOT NULL,
  full_name VARCHAR(255) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('attendee', 'organiser', 'admin') NOT NULL DEFAULT 'attendee',
  account_status ENUM('active', 'suspended') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (user_id),
 UNIQUE KEY uq_users_email (email)
 )ENGINE= InnoDB;

-- Organiser Profile Table
CREATE TABLE organiser_profiles (
  organiser_id INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  user_id INT UNSIGNED NOT NULL,
  org_name VARCHAR(150) NOT NULL,
  description TEXT NULL,
  contact_email VARCHAR(255) NULL,
  contact_phone VARCHAR(30) NULL,
  is_trusted TINYINT(1) NOT NULL DEFAULT 0, -- AR-09(Could have)
 PRIMARY KEY (organiser_id),
 UNIQUE KEY uq_organiser_user (user_id),
  CONSTRAINT fk_organiser_user
   FOREIGN KEY (user_id) REFERENCES users(user_id)

)ENGINE = InnoDB;

-- Organiser Social Links Table 
CREATE TABLE organiser_social_links (
    link_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    organiser_id INT UNSIGNED NOT NULL,
    platform VARCHAR(50) NOT NULL,
    url VARCHAR(500) NOT NULL,
    PRIMARY KEY (link_id),
    KEY idx_social_organiser_id (organiser_id),
    CONSTRAINT fk_social_organiser FOREIGN KEY (organiser_id) REFERENCES organiser_profiles(organiser_id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- Categories Table
CREATE TABLE categories(
 category_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 name VARCHAR(100) NOT NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 PRIMARY KEY(category_id),
 UNIQUE KEY uq_categories_name(name)
)ENGINE=InnoDB;

-- User Interests Table
CREATE TABLE user_interests (
    user_id INT UNSIGNED NOT NULL,
    category_id INT UNSIGNED NOT NULL,
    PRIMARY KEY(user_id, category_id),
    KEY idx_interests_category(category_id),
    CONSTRAINT fk_interest_user
    FOREIgN KEY(user_id) REFERENCES users(user_id)
    ON DELETE CASCADE,
    CONSTRAINT fk_interest_category
    FOREIGN KEY(category_id)REFERENCES categories(category_id)
    ON DELETE CASCADE
)ENGINE=InnoDB;

-- EVENT SERIES TABLE 
CREATE TABLE event_series (
    series_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    organiser_id INT UNSIGNED NOT NULL,
    recurrence_type ENUM( 'weekly', 'monthly') NOT NULL,
    start_date DATE NOT NULL ,
    end_DATE DATE  NULL, -- NULL = open-ended series
    start_time TIME NOT NULL, -- each sesssion must have a start time
    end_time TIME NOT NULL,
    status ENUM('pending_approval','active','ended') NOT NULL DEFAULT 'pending_approval',
    generated_until DATE  NULL, -- how far occerences have been created (SYS - 04)
    last_confirmed_at DATETIME  NULL ,
    next_prompt_at DATETIME  NULL,
    prompt_sent_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(series_id),
    KEY idx_series_organiser(organiser_id),
    KEY idx_series_prompt(status, next_prompt_at),
    CONSTRAINT fk_series_organiser
    FOREIGN KEY(organiser_id) REFERENCES organiser_profiles(organiser_id),
    CONSTRAINT chk_series_dates CHECK(end_date IS NULL OR end_date >=start_date)
)ENGINE=InnoDB;


-- Events Table
CREATE TABLE events(
  event_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  organiser_id INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  series_id INT UNSIGNED  NULL, -- NULL for one-off events
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  poster_path VARCHAR(500) NOT NULL,
  video_url VARCHAR(500) NULL, -- Not all posts MUST have a promotional video
  venue_name VARCHAR(200) NOT NULL,
  address VARCHAR(300) NOT NULL,
  price_type ENUM('amount', 'range','free') NOT NULL,  -- BR-20
  price_min DECIMAL(10,2) NULL ,
  price_max DECIMAL(10,2) NULL,
  ticket_url VARCHAR(500) NULL,
  ticket_note VARCHAR(300) NULL ,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  status ENUM('draft','pending','published','rejected','removed','cancelled','archived') NOT NULL DEFAULT 'draft',
  cancelled_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
             ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(event_id),
  KEY idx_events_discovery (status, start_datetime),
  KEY idx_events_end(status, end_datetime),
  KEY idx_events_organiser(organiser_id),
  KEY idx_events_category (category_id),
  KEY idx_events_series (series_id),
   CONSTRAINT fk_events_organiser
    FOREIGN KEY (organiser_id) REFERENCES organiser_profiles (organiser_id),
  CONSTRAINT fk_events_category
    FOREIGN KEY (category_id) REFERENCES categories (category_id),
  CONSTRAINT fk_events_series
    FOREIGN KEY (series_id) REFERENCES event_series (series_id)
    ON DELETE SET NULL,
  CONSTRAINT chk_event_times CHECK (end_datetime >= start_datetime),
  CONSTRAINT chk_event_price CHECK (
     (price_type= 'free' AND price_min IS NULL AND  price_max IS NULL) OR
     (price_type='amount' AND price_min IS NOT NULL AND price_max IS NULL) OR
     (price_type= 'range' AND price_min IS NOT NULL AND price_max IS NOT NULL AND price_max>= price_min)
  )
)ENGINE=InnoDB;

-- Event Approved Versions Table 
CREATE TABLE event_approved_versions (
  event_id     INT UNSIGNED NOT NULL,
  title        VARCHAR(200) NOT NULL,
  category_id  INT UNSIGNED NOT NULL,
  description  TEXT NOT NULL,
  poster_path  VARCHAR(500) NOT NULL,
  approved_by  INT UNSIGNED NOT NULL,
  approved_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (event_id),
  KEY idx_approved_category (category_id),
  KEY idx_approved_by (approved_by),
  CONSTRAINT fk_approved_event
    FOREIGN KEY (event_id) REFERENCES events (event_id)
    ON DELETE CASCADE,
  CONSTRAINT fk_approved_category
    FOREIGN KEY (category_id) REFERENCES categories (category_id),
  CONSTRAINT fk_approved_admin
    FOREIGN KEY (approved_by) REFERENCES users (user_id)
) ENGINE=InnoDB;


-- Event Images Table
CREATE TABLE event_images(
    image_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    sort_order INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY(image_id),
    KEY idx_images_event(event_id,sort_order),
    CONSTRAINT fk_images_event
    FOREIGN KEY(event_id) REFERENCES events(event_id)
    ON DELETE CASCADE

)ENGINE=InnoDB;

-- Moderation Actions Table
CREATE TABLE moderation_actions(
    action_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id INT UNSIGNED NOT NULL,
    admin_id INT UNSIGNED NOT NULL,
    action_type ENUM('approve','reject','remove','restore','archive') NOT NULL,
    reason TEXT  NULL, 
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(action_id),
    KEY idx_mod_event(event_id,created_at),
    KEY idx_mod_admin(admin_id),
    CONSTRAINT fk_mod_event
    FOREIGN KEY(event_id) REFERENCES events(event_id)
    ON DELETE CASCADE,
    CONSTRAINT fk_mod_admin
    FOREIGN KEY(admin_id) REFERENCES users(user_id)
)ENGINE=InnoDB;

-- Event Change Log Table 
CREATE TABLE event_change_log(
    log_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    event_id INT UNSIGNED NOT NULL,
    changed_by INT UNSIGNED NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    old_value TEXT NULL,
    new_value TEXT NULL,
    changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(log_id),
    KEY idx_log_event(event_id,changed_at),
    KEY idx_log_user(changed_by),
    CONSTRAINT fk_log_event
    FOREIGN KEY(event_id) REFERENCES events(event_id)
    ON DELETE CASCADE,
    CONSTRAINT fk_log_user
    FOREIGN KEY(changed_by)REFERENCES users(user_id)
)ENGINE=InnoDB;

-- Saved Events Table 
CREATE TABLE saved_events(
    user_id INT UNSIGNED NOT NULL,
    event_id INT UNSIGNED NOT NULL,
    saved_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(user_id, event_id),
    KEY idx_saved_event(event_id),
    CONSTRAINT fk_saved_user
    FOREIGN KEY(user_id)REFERENCES users(user_id)
    ON DELETE CASCADE,
    CONSTRAINT fk_saved_event
    FOREIGN KEY(event_id)REFERENCES events(event_id)
    ON DELETE CASCADE
)ENGINE=InnoDB;

-- Calendar Entries Table 

-- THIS IS A JUNCTION TABLE BETWEEN USERS AND EVENTS, REPRESENTING EVENTS ADDED TO A USER'S CALENDAR ----- UR -09
CREATE TABLE calendar_entries(
     user_id INT UNSIGNED NOT NULL,
     event_id INT UNSIGNED NOT NULL,
     added_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY(user_id, event_id),
     KEY idx_calendar_event(event_id),
     CONSTRAINT fk_calendar_user
     FOREIGN KEY(user_id) REFERENCES users(user_id)
     ON DELETE CASCADE,
     CONSTRAINT fk_calendar_event
     FOREIGN KEY(event_id) REFERENCES events(event_id)
     ON DELETE CASCADE
)ENGINE=InnoDB;

-- Reminders Table 
CREATE TABLE reminders(
   reminder_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
   user_id INT UNSIGNED NOT NULL,
   event_id INT UNSIGNED NOT NULL,
   lead_minutes INT UNSIGNED NOT NULL, -- how many minutes before the event to send the reminder
   remind_at DATETIME NOT NULL,
   status ENUM('pending', 'sent')NOT NULL DEFAULT 'pending',
   PRIMARY KEY(reminder_id),
   UNIQUE KEY uq_reminder_user_event(user_id, event_id, lead_minutes),
   KEY idx_reminder_due(status, remind_at),
   KEY idx_reminder_event(event_id),
   CONSTRAINT fk_reminder_user
    FOREIGN KEY(user_id)REFERENCES users(user_id)
      ON DELETE CASCADE,
    CONSTRAINT fk_reminder_event
     FOREIGN KEY(event_id)REFERENCES events(event_id)
     ON DELETE CASCADE
)ENGINE=InnoDB;

-- Notifications Table 

-- An in-platform notification panel, event_id and series_id are optional
CREATE TABLE notifications(
 notification_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
 user_id INT UNSIGNED NOT NULL,
 event_id INT UNSIGNED  NULL,
 series_id INT UNSIGNED NULL,
 notification_type ENUM('cancelled','back_on', 'changed','reminder', 'series_prompt')NOT NULL,
 message VARCHAR(500) NOT NULL,
 is_read TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY(notification_id),
 KEY idx_notification_user(user_id, is_read, created_at),
 KEY idx_notification_event(event_id),
 KEY idx_notification_series(series_id),
 CONSTRAINT fk_notification_user
    FOREIGN KEY(user_id)REFERENCES users(user_id)
    ON DELETE CASCADE,
    CONSTRAINT fk_notification_event
     FOREIGN KEY(event_id)REFERENCES events(event_id)
     ON DELETE CASCADE,
     CONSTRAINT fk_notification_series
     FOREIGN KEY(series_id)REFERENCES event_series(series_id)
     ON DELETE CASCADE
)ENGINE=InnoDB;

-- Event Stats Daily Table
CREATE TABLE event_stats_daily(
   stat_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
   event_id INT UNSIGNED NOT NULL,
   stat_date DATE NOT NULL,
   views INT UNSIGNED NOT NULL DEFAULT 0,
   saves INT UNSIGNED NOT NULL DEFAULT 0,
   calendar_adds INT UNSIGNED NOT NULL DEFAULT 0,
   PRIMARY KEY(stat_id),
   UNIQUE KEY uq_stats_event_date(event_id,stat_date),
   CONSTRAINT fk_stat_event
    FOREIGN KEY(event_id)REFERENCES events(event_id)
    ON DELETE CASCADE
)ENGINE=InnoDB;

-- App Settings Table
CREATE TABLE app_settings(
 setting_key VARCHAR(100) NOT NULL ,
  setting_value VARCHAR(500) NOT NULL,
  PRIMARY KEY(setting_key)
)ENGINE=InnoDB;

-- Starting Data 

-- INSERT

-- Starting categories 
INSERT INTO categories(name) VALUES
('Music and Nightlife'),
('Networking'),
('Workshops and Learning'),
('Markets and Food'),
('Arts and Culture'),
('Sports and Fitness'),
('Community and Charity');

-- Starting settings (CAN BE CHANGED) 
INSERT INTO app_settings(setting_key,setting_value) VALUES
('cancelled_feed_hours', '24'),  -- BR-09 / SYS-07
('occurrence_weeks_ahead', '8'), -- SYS-04
('series_prompt_months', '3'); -- BR-11 / SYS-05

-- The first administrator is created by the system owner (OQ-10), not by
-- public registration. Insert one manually with a hash from password_hash() in PHP:
-- INSERT INTO users (full_name, email, password_hash, role)
-- VALUES ('Admin', 'admin@example.com', '<paste_password_hash_here>', 'admin');




SHOW TABLES;