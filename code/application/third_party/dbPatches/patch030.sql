ALTER TABLE `usermember` MODIFY COLUMN `id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
 MODIFY COLUMN `email` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
 ALTER TABLE `usermemberprofile` MODIFY COLUMN `gender` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
