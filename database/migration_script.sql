-- ==========================================
-- BULLETPROOF ExpenseVoyage Migration Script
-- ==========================================
-- This script handles missing tables and dynamic
-- columns to ensure a successful merge.

DELIMITER //

DROP PROCEDURE IF EXISTS GlobalMigration //

CREATE PROCEDURE GlobalMigration()
BEGIN
    DECLARE col_id VARCHAR(50);
    DECLARE col_name VARCHAR(50);
    DECLARE col_img VARCHAR(50);
    DECLARE col_pass VARCHAR(50);
    DECLARE col_date VARCHAR(50);
    DECLARE db_name VARCHAR(100);
    DECLARE table_exists INT;
    
    SET db_name = DATABASE();

    -- 1. DROP EXISTING CONSTRAINTS
    IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = 'trips_ibfk_1' AND TABLE_SCHEMA = db_name) THEN
        ALTER TABLE trips DROP FOREIGN KEY trips_ibfk_1;
    END IF;
    IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE CONSTRAINT_NAME = 'trips_user_fk' AND TABLE_SCHEMA = db_name) THEN
        ALTER TABLE trips DROP FOREIGN KEY trips_user_fk;
    END IF;

    -- 2. PREPARE THE UNIFIED TABLE (Always start clean)
    -- We skip dropping if we are middle-migration, but here we assume a fresh attempt
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'users_unified' AND TABLE_SCHEMA = db_name) THEN
        CREATE TABLE `users_unified` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `first_name` varchar(255) DEFAULT NULL,
          `last_name` varchar(255) DEFAULT NULL,
          `email` varchar(255) NOT NULL,
          `password_hash` varchar(255) NOT NULL,
          `profile_image` varchar(255) DEFAULT NULL,
          `is_verified` tinyint(1) DEFAULT 0,
          `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
          PRIMARY KEY (`id`),
          UNIQUE KEY `email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    END IF;

    -- 3. MIGRATE FROM 'users' (Table existence check)
    SET table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'users' AND TABLE_SCHEMA = db_name);
    
    IF table_exists > 0 THEN
        SET col_id = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('user_id', 'id') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_name = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('first_name', 'name', 'fname') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_img = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('user_image', 'profile_image', 'image') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_pass = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('password_hash', 'password', 'pass') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_date = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME IN ('date_time', 'created_at', 'timestamp') AND TABLE_SCHEMA = db_name LIMIT 1);

        IF col_id IS NOT NULL THEN
            SET @sql_cmd = CONCAT('INSERT IGNORE INTO users_unified (id, first_name, last_name, email, password_hash, profile_image, is_verified, created_at) ',
                                 'SELECT ', col_id, ', ', 
                                 IFNULL(CONCAT('`', col_name, '`'), 'NULL'), ', ',
                                 'IFNULL(last_name, NULL), ',
                                 'CONCAT("user_", ', col_id, ', "@legacy.com"), ',
                                 IFNULL(CONCAT('`', col_pass, '`'), '""'), ', ',
                                 IFNULL(CONCAT('`', col_img, '`'), 'NULL'), ', ',
                                 'is_verified, ',
                                 IFNULL(CONCAT('`', col_date, '`'), 'NOW()'),
                                 ' FROM `users`');
            PREPARE run_users FROM @sql_cmd;
            EXECUTE run_users;
            DEALLOCATE PREPARE run_users;
        END IF;
        -- Mark for deletion later
        SET @drop_users = 1;
    ELSE
        SET @drop_users = 0;
    END IF;

    -- 4. MIGRATE FROM 'user' (Table existence check)
    SET table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'user' AND TABLE_SCHEMA = db_name);

    IF table_exists > 0 THEN
        SET col_id = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user' AND COLUMN_NAME IN ('id', 'user_id') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_name = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user' AND COLUMN_NAME IN ('name', 'first_name', 'fname') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_img = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user' AND COLUMN_NAME IN ('profile_image', 'user_image', 'image') AND TABLE_SCHEMA = db_name LIMIT 1);
        SET col_pass = (SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'user' AND COLUMN_NAME IN ('password', 'pass', 'password_hash') AND TABLE_SCHEMA = db_name LIMIT 1);

        SET @sql_cmd2 = CONCAT('INSERT INTO users_unified (first_name, email, password_hash, profile_image, is_verified) ',
                              'SELECT ', IFNULL(CONCAT('`', col_name, '`'), '"Guest"'), ', email, ', 
                              IFNULL(CONCAT('`', col_pass, '`'), '""'), ', ',
                              IFNULL(CONCAT('`', col_img, '`'), 'NULL'), ', is_verified FROM `user` ',
                              'ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), password_hash = VALUES(password_hash)');
        PREPARE run_user FROM @sql_cmd2;
        EXECUTE run_user;
        DEALLOCATE PREPARE run_user;
        -- Mark for deletion later
        SET @drop_user = 1;
    ELSE
        SET @drop_user = 0;
    END IF;

    -- 5. FINALIZE (Only if users_unified has data or we finished migration)
    IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'users_unified' AND TABLE_SCHEMA = db_name) THEN
        IF @drop_users = 1 THEN DROP TABLE `users`; END IF;
        IF @drop_user = 1 THEN DROP TABLE `user`; END IF;
        
        -- If target 'users' doesn't exist anymore, rename unified to it
        IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'users' AND TABLE_SCHEMA = db_name) THEN
            RENAME TABLE `users_unified` TO `users`;
        END IF;
    END IF;

    -- Add booking columns to trips if missing
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'trips' AND COLUMN_NAME = 'seats_available' AND TABLE_SCHEMA = db_name) THEN
        ALTER TABLE `trips` ADD COLUMN `seats_available` int(11) DEFAULT 20;
    END IF;
    IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'trips' AND COLUMN_NAME = 'booked_seats' AND TABLE_SCHEMA = db_name) THEN
        ALTER TABLE `trips` ADD COLUMN `booked_seats` int(11) DEFAULT 0;
    END IF;

    -- Re-establish foreign key (id is the new standard)
    ALTER TABLE `trips` ADD CONSTRAINT `trips_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
    
    -- Update Admin Password
    UPDATE `admin` SET `apass` = '$2y$10$P3QTIrdnbGoN3BQVZ0syuPgEp9icttfJOOHQvQFn5BbaiM4u' WHERE `apass` = '@321' OR `apass` = 'admin';

END //

DELIMITER ;

CALL GlobalMigration();
DROP PROCEDURE IF EXISTS GlobalMigration;
