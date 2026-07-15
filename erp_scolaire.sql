-- ============================================================
--  ERP Scolaire — Schéma complet MySQL
--  Ordre de création respectant les contraintes FK
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ------------------------------------------------------------
-- 1. users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`         BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100)     NOT NULL,
    `email`      VARCHAR(180)     NOT NULL,
    `password`   VARCHAR(255)     NOT NULL,
    `phone`      VARCHAR(20)      NULL,
    `role`       ENUM('admin','teacher','student','parent') NOT NULL DEFAULT 'student',
    `is_active`  TINYINT(1)       NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP        NULL,
    `updated_at` TIMESTAMP        NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `users_email_unique` (`email`),
    INDEX `users_role_index` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. password_reset_tokens  (Laravel Breeze standard)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
    `email`      VARCHAR(180) NOT NULL,
    `token`      VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP    NULL,
    PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. classes
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `classes` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(60)     NOT NULL,
    `level`         VARCHAR(40)     NOT NULL,
    `capacity`      SMALLINT UNSIGNED NOT NULL DEFAULT 40,
    `academic_year` YEAR            NOT NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `classes_name_year_unique` (`name`, `academic_year`),
    INDEX `classes_academic_year_index` (`academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. subjects
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `subjects` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(80)      NOT NULL,
    `code`        VARCHAR(20)      NOT NULL,
    `coefficient` DECIMAL(4,2)     NOT NULL DEFAULT 1.00,
    `created_at`  TIMESTAMP        NULL,
    `updated_at`  TIMESTAMP        NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `subjects_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. teachers
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `teachers` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     BIGINT UNSIGNED NOT NULL,
    `specialty`   VARCHAR(100)    NULL,
    `created_at`  TIMESTAMP       NULL,
    `updated_at`  TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `teachers_user_id_unique` (`user_id`),
    CONSTRAINT `teachers_user_id_fk`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. students
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `students` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`        BIGINT UNSIGNED NOT NULL,
    `class_id`       BIGINT UNSIGNED NOT NULL,
    `matricule`      VARCHAR(30)     NOT NULL,
    `birth_date`     DATE            NOT NULL,
    `gender`         ENUM('M','F')   NOT NULL,
    `photo_path`     VARCHAR(255)    NULL,
    `guardian_name`  VARCHAR(100)    NULL,
    `guardian_phone` VARCHAR(20)     NULL,
    `academic_year`  YEAR            NOT NULL,
    `created_at`     TIMESTAMP       NULL,
    `updated_at`     TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `students_matricule_unique` (`matricule`),
    UNIQUE KEY `students_user_id_unique` (`user_id`),
    INDEX `students_class_id_index` (`class_id`),
    INDEX `students_academic_year_index` (`academic_year`),
    CONSTRAINT `students_user_id_fk`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `students_class_id_fk`
        FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. parents_users  (pivot user ↔ student)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `parents_users` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP       NULL,
    `updated_at` TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `parents_users_user_student_unique` (`user_id`, `student_id`),
    CONSTRAINT `parents_users_user_id_fk`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `parents_users_student_id_fk`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. class_subject_teacher  (pivot classe × matière × enseignant)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `class_subject_teacher` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `class_id`      BIGINT UNSIGNED NOT NULL,
    `subject_id`    BIGINT UNSIGNED NOT NULL,
    `teacher_id`    BIGINT UNSIGNED NOT NULL,
    `academic_year` YEAR            NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `cst_unique` (`class_id`, `subject_id`, `academic_year`),
    INDEX `cst_teacher_index` (`teacher_id`),
    CONSTRAINT `cst_class_id_fk`
        FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cst_subject_id_fk`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `cst_teacher_id_fk`
        FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. grades
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grades` (
    `id`            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `student_id`    BIGINT UNSIGNED  NOT NULL,
    `subject_id`    BIGINT UNSIGNED  NOT NULL,
    `class_id`      BIGINT UNSIGNED  NOT NULL,
    `score`         DECIMAL(5,2)     NOT NULL,
    `max_score`     DECIMAL(5,2)     NOT NULL DEFAULT 20.00,
    `type`          ENUM('devoir','interrogation','examen') NOT NULL,
    `period`        TINYINT UNSIGNED NOT NULL COMMENT '1=T1 2=T2 3=T3',
    `academic_year` YEAR             NOT NULL,
    `comment`       TEXT             NULL,
    `created_at`    TIMESTAMP        NULL,
    `updated_at`    TIMESTAMP        NULL,
    PRIMARY KEY (`id`),
    INDEX `grades_student_subject_period_index` (`student_id`, `subject_id`, `period`),
    INDEX `grades_academic_year_index` (`academic_year`),
    CONSTRAINT `grades_student_id_fk`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `grades_subject_id_fk`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `grades_class_id_fk`
        FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. attendances
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `attendances` (
    `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id` BIGINT UNSIGNED NOT NULL,
    `class_id`   BIGINT UNSIGNED NOT NULL,
    `teacher_id` BIGINT UNSIGNED NOT NULL,
    `date`       DATE            NOT NULL,
    `status`     ENUM('present','absent','late') NOT NULL DEFAULT 'present',
    `reason`     TEXT            NULL,
    `created_at` TIMESTAMP       NULL,
    `updated_at` TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `attendances_student_date_unique` (`student_id`, `date`),
    INDEX `attendances_class_date_index` (`class_id`, `date`),
    CONSTRAINT `attendances_student_id_fk`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `attendances_class_id_fk`
        FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `attendances_teacher_id_fk`
        FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. schedules
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `schedules` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `class_id`    BIGINT UNSIGNED  NOT NULL,
    `subject_id`  BIGINT UNSIGNED  NOT NULL,
    `teacher_id`  BIGINT UNSIGNED  NOT NULL,
    `day_of_week` TINYINT UNSIGNED NOT NULL COMMENT '0=Lun 1=Mar 2=Mer 3=Jeu 4=Ven',
    `start_time`  TIME             NOT NULL,
    `end_time`    TIME             NOT NULL,
    `created_at`  TIMESTAMP        NULL,
    `updated_at`  TIMESTAMP        NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `schedules_class_day_time_unique` (`class_id`, `day_of_week`, `start_time`),
    INDEX `schedules_teacher_day_index` (`teacher_id`, `day_of_week`),
    CONSTRAINT `schedules_class_id_fk`
        FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `schedules_subject_id_fk`
        FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `schedules_teacher_id_fk`
        FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 12. fee_types
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `fee_types` (
    `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`          VARCHAR(100)    NOT NULL,
    `amount`        DECIMAL(10,2)   NOT NULL,
    `academic_year` YEAR            NOT NULL,
    `created_at`    TIMESTAMP       NULL,
    `updated_at`    TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `fee_types_name_year_unique` (`name`, `academic_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 13. payments
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `payments` (
    `id`             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `student_id`     BIGINT UNSIGNED NOT NULL,
    `fee_type_id`    BIGINT UNSIGNED NOT NULL,
    `created_by`     BIGINT UNSIGNED NOT NULL COMMENT 'admin user_id',
    `amount_paid`    DECIMAL(10,2)   NOT NULL,
    `receipt_number` VARCHAR(40)     NOT NULL,
    `status`         ENUM('pending','paid','partial') NOT NULL DEFAULT 'pending',
    `paid_at`        DATE            NULL,
    `note`           TEXT            NULL,
    `created_at`     TIMESTAMP       NULL,
    `updated_at`     TIMESTAMP       NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `payments_receipt_unique` (`receipt_number`),
    INDEX `payments_student_status_index` (`student_id`, `status`),
    CONSTRAINT `payments_student_id_fk`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `payments_fee_type_id_fk`
        FOREIGN KEY (`fee_type_id`) REFERENCES `fee_types` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `payments_created_by_fk`
        FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 14. report_cards
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `report_cards` (
    `id`            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `student_id`    BIGINT UNSIGNED  NOT NULL,
    `class_id`      BIGINT UNSIGNED  NOT NULL,
    `period`        TINYINT UNSIGNED NOT NULL COMMENT '1=T1 2=T2 3=T3',
    `academic_year` YEAR             NOT NULL,
    `average`       DECIMAL(5,2)     NOT NULL,
    `rank`          SMALLINT UNSIGNED NULL,
    `appreciation`  VARCHAR(80)      NULL,
    `pdf_path`      VARCHAR(255)     NULL,
    `created_at`    TIMESTAMP        NULL,
    `updated_at`    TIMESTAMP        NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `report_cards_student_period_year_unique` (`student_id`, `period`, `academic_year`),
    INDEX `report_cards_class_period_index` (`class_id`, `period`, `academic_year`),
    CONSTRAINT `report_cards_student_id_fk`
        FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `report_cards_class_id_fk`
        FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
