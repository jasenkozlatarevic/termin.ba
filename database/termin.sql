
-- =====================================================
-- Termin.ba - Milestone 2
-- Database schema and sample data
-- =====================================================

CREATE DATABASE IF NOT EXISTS termin_ba
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE termin_ba;

-- Disable foreign key checks while recreating tables
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS game_participants;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS games;
DROP TABLE IF EXISTS venues;
DROP TABLE IF EXISTS users;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. USERS
-- =====================================================

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- 2. VENUES
-- =====================================================

CREATE TABLE venues (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255) NOT NULL,
    sport VARCHAR(50) NOT NULL DEFAULT 'Football',
    price_per_hour DECIMAL(10,2) NOT NULL,
    description TEXT,
    CONSTRAINT chk_venue_price CHECK (price_per_hour >= 0)
) ENGINE=InnoDB;

-- =====================================================
-- 3. GAMES
-- =====================================================

CREATE TABLE games (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    creator_id INT UNSIGNED NOT NULL,
    venue_id INT UNSIGNED NOT NULL,
    game_name VARCHAR(150) NOT NULL,
    game_date DATE NOT NULL,
    game_time TIME NOT NULL,
    max_players INT NOT NULL,
    skill_level VARCHAR(50) NOT NULL DEFAULT 'All levels',
    description TEXT,

    CONSTRAINT chk_game_max_players
        CHECK (max_players > 0),

    CONSTRAINT fk_games_creator
        FOREIGN KEY (creator_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_games_venue
        FOREIGN KEY (venue_id)
        REFERENCES venues(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 4. RESERVATIONS
-- =====================================================

CREATE TABLE reservations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    venue_id INT UNSIGNED NOT NULL,
    reservation_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    number_of_players INT NOT NULL,

    CONSTRAINT chk_reservation_time
        CHECK (end_time > start_time),

    CONSTRAINT chk_reservation_players
        CHECK (number_of_players > 0),

    CONSTRAINT fk_reservations_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_reservations_venue
        FOREIGN KEY (venue_id)
        REFERENCES venues(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 5. GAME PARTICIPANTS
-- =====================================================

CREATE TABLE game_participants (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    game_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    joined_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_game_participant
        UNIQUE (game_id, user_id),

    CONSTRAINT fk_participants_game
        FOREIGN KEY (game_id)
        REFERENCES games(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_participants_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- 6. REVIEWS
-- =====================================================

CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    venue_id INT UNSIGNED NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT chk_review_rating
        CHECK (rating BETWEEN 1 AND 5),

    CONSTRAINT fk_reviews_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    CONSTRAINT fk_reviews_venue
        FOREIGN KEY (venue_id)
        REFERENCES venues(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =====================================================
-- SAMPLE DATA
-- =====================================================

INSERT INTO users (full_name, email, password) VALUES
('Amar Hadzic', 'amar@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'),
('Adnan Kovac', 'adnan@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.'),
('Haris Basic', 'haris@example.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.');

INSERT INTO venues
    (name, location, sport, price_per_hour, description)
VALUES
('Football Arena Sarajevo', 'Sarajevo, Grbavica',
 'Football', 80.00, 'Outdoor football pitch.'),
('City Sports Center', 'Sarajevo, Otoka',
 'Football', 100.00, 'Football pitch with changing rooms.'),
('Green Field', 'Sarajevo, Ilidza',
 'Football', 70.00, 'Outdoor recreational football pitch.');

INSERT INTO games
    (creator_id, venue_id, game_name, game_date, game_time,
     max_players, skill_level, description)
VALUES
(1, 1, 'Friday Football', '2026-11-06', '18:00:00',
 10, 'Intermediate', 'Friendly evening football game.'),
(2, 2, 'Weekend Match', '2026-11-07', '16:00:00',
 14, 'All levels', 'Open game for recreational players.');

INSERT INTO reservations
    (user_id, venue_id, reservation_date, start_time,
     end_time, number_of_players)
VALUES
(1, 1, '2026-11-06', '18:00:00', '19:00:00', 10),
(2, 3, '2026-11-08', '17:00:00', '18:30:00', 8);

INSERT INTO game_participants (game_id, user_id) VALUES
(1, 1),
(1, 2),
(2, 2),
(2, 3);

INSERT INTO reviews (user_id, venue_id, rating, comment) VALUES
(1, 1, 5, 'Great pitch and good facilities.'),
(2, 1, 4, 'Good place to play football.'),
(3, 2, 5, 'Excellent sports center.');

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

SELECT 'users' AS table_name, COUNT(*) AS total FROM users
UNION ALL
SELECT 'venues', COUNT(*) FROM venues
UNION ALL
SELECT 'games', COUNT(*) FROM games
UNION ALL
SELECT 'reservations', COUNT(*) FROM reservations
UNION ALL
SELECT 'game_participants', COUNT(*) FROM game_participants
UNION ALL
SELECT 'reviews', COUNT(*) FROM reviews;
