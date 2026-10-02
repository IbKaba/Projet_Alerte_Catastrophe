-- Schéma de référence MySQL - Alerte Catastrophe
-- IMPORTANT : les migrations Laravel dans database/migrations/ sont la source de vérité.
-- Ce fichier sert uniquement de référence documentaire et ne remplace pas `php artisan migrate`.

CREATE TABLE disaster_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE disasters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    instructions TEXT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY uq_disaster_category_name (category_id, name),
    CONSTRAINT fk_disaster_category FOREIGN KEY (category_id) REFERENCES disaster_categories(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- La table users est créée par la migration Laravel standard puis enrichie de phone, role et is_active.
-- Les mots de passe sont toujours stockés hashés par Laravel.

CREATE TABLE alerts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    disaster_id BIGINT UNSIGNED NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    address VARCHAR(255) NULL,
    google_place_id VARCHAR(255) NULL,
    description TEXT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    rejection_reason TEXT NULL,
    validated_by BIGINT UNSIGNED NULL,
    validated_at TIMESTAMP NULL,
    resolved_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_alert_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    CONSTRAINT fk_alert_disaster FOREIGN KEY (disaster_id) REFERENCES disasters(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_alert_moderator FOREIGN KEY (validated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE alert_photos (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alert_id BIGINT UNSIGNED NOT NULL,
    path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NULL,
    position TINYINT UNSIGNED NOT NULL DEFAULT 1,
    UNIQUE KEY uq_alert_photo_position (alert_id, position),
    CONSTRAINT fk_alert_photo_alert FOREIGN KEY (alert_id) REFERENCES alerts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    entity_type VARCHAR(255) NULL,
    entity_id BIGINT UNSIGNED NULL,
    metadata JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
