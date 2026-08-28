-- Run this in every live tenant database before deploying the login-audit code.
-- This is intentionally a standalone SQL script; no Laravel migration is required.

CREATE TABLE IF NOT EXISTS `user_login_audits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NULL,
    `username` VARCHAR(191) NOT NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `logged_in_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_login_audits_user_id_index` (`user_id`),
    KEY `user_login_audits_username_logged_in_at_index` (`username`, `logged_in_at`),
    KEY `user_login_audits_logged_in_at_index` (`logged_in_at`),
    CONSTRAINT `user_login_audits_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verification query:
SELECT
    `id`,
    `user_id`,
    `username`,
    `ip_address`,
    `logged_in_at`
FROM `user_login_audits`
ORDER BY `logged_in_at` DESC, `id` DESC
LIMIT 100;
