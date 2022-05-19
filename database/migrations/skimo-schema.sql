-- 1
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_token` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_api_token_unique` (`api_token`)
);

-- 2
CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
);

-- 3
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);

-- 4
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isU23` tinyint(1) NOT NULL DEFAULT '0',
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent` bigint unsigned DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_parent_foreign` (`parent`),
  CONSTRAINT `categories_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `categories` (`id`) ON DELETE CASCADE
);

-- 5
CREATE TABLE `countries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flagImage` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- 6
CREATE TABLE `athletes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `firstName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastName` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countryId` bigint unsigned DEFAULT NULL,
  `placeOfBirth` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dateOfBirth` date DEFAULT NULL,
  `favoriteRace` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favoriteRaceId` bigint unsigned DEFAULT NULL,
  `socialLinks` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` int DEFAULT NULL,
  `weight` int DEFAULT NULL,
  `category` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clicks` bigint unsigned NOT NULL DEFAULT '0',
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendsLausanne` tinyint(1) DEFAULT NULL,
  `show_in_api` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `athletes_slug_unique` (`slug`),
  KEY `athletes_countryid_index` (`countryId`),
  CONSTRAINT `athletes_countryid_foreign` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE SET NULL
);

-- 7
CREATE TABLE `races` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `place` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rankingCategoryId` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `races_rankingcategoryid_foreign` (`rankingCategoryId`),
  CONSTRAINT `races_rankingcategoryid_foreign` FOREIGN KEY (`rankingCategoryId`) REFERENCES `ranking_categories` (`id`) ON DELETE SET NULL
);

-- 8
CREATE TABLE `race_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `raceId` bigint unsigned NOT NULL,
  `startDate` timestamp NULL DEFAULT NULL,
  `endDate` timestamp NULL DEFAULT NULL,
  `year` int DEFAULT NULL,
  `elevation` int DEFAULT NULL,
  `place` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` bigint unsigned DEFAULT NULL,
  `parent` bigint unsigned DEFAULT NULL,
  `hasStages` tinyint(1) NOT NULL DEFAULT '0',
  `isTeam` tinyint(1) NOT NULL DEFAULT '0',
  `is_visible` tinyint(1) NOT NULL DEFAULT '0',
  `optionalName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countryId` bigint unsigned DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rankingCategoryId` bigint unsigned DEFAULT NULL,
  `stageNumber` int DEFAULT NULL,
  `isGeneralClassification` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `race_events_slug_unique` (`slug`),
  KEY `race_events_parent_foreign` (`parent`),
  KEY `race_events_raceid_foreign` (`raceId`),
  KEY `race_events_countryid_foreign` (`countryId`),
  KEY `race_events_rankingcategoryid_foreign` (`rankingCategoryId`),
  CONSTRAINT `race_events_countryid_foreign` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `race_events_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `race_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_events_raceid_foreign` FOREIGN KEY (`raceId`) REFERENCES `races` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_events_rankingcategoryid_foreign` FOREIGN KEY (`rankingCategoryId`) REFERENCES `ranking_categories` (`id`) ON DELETE SET NULL
);

-- 9
CREATE TABLE `upcoming_races` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `athleteId` bigint unsigned NOT NULL,
  `eventId` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `upcoming_races_athleteid_foreign` (`athleteId`),
  KEY `upcoming_races_eventid_foreign` (`eventId`),
  CONSTRAINT `upcoming_races_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `upcoming_races_eventid_foreign` FOREIGN KEY (`eventId`) REFERENCES `race_events` (`id`) ON DELETE CASCADE
);

-- 10
CREATE TABLE `race_event_start_list` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `athleteId` bigint unsigned NOT NULL,
  `categoryId` bigint unsigned DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `race_event_start_list_categoryid_foreign` (`categoryId`),
  KEY `race_event_start_list_athleteid_foreign` (`athleteId`),
  CONSTRAINT `race_event_start_list_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_start_list_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE
);

-- 11
CREATE TABLE `teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `countryId` bigint unsigned DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `teams_countryid_foreign` (`countryId`),
  CONSTRAINT `teams_countryid_foreign` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE SET NULL
);

-- 12
CREATE TABLE `team_athletes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `athleteId` bigint unsigned NOT NULL,
  `teamId` bigint unsigned NOT NULL,
  `countryId` bigint unsigned DEFAULT NULL,
  `positionName` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `team_athletes_countryid_foreign` (`countryId`),
  KEY `team_athletes_athleteid_foreign` (`athleteId`),
  KEY `team_athletes_teamid_foreign` (`teamId`),
  CONSTRAINT `team_athletes_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `team_athletes_countryid_foreign` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `team_athletes_teamid_foreign` FOREIGN KEY (`teamId`) REFERENCES `teams` (`id`) ON DELETE CASCADE
);

-- 13
CREATE TABLE `race_event_teams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `teamId` bigint unsigned DEFAULT NULL,
  `raceEventId` bigint unsigned NOT NULL,
  `categoryId` bigint unsigned NOT NULL,
  `countryId` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `race_event_teams_teamid_foreign` (`teamId`),
  KEY `race_event_teams_raceeventid_foreign` (`raceEventId`),
  KEY `race_event_teams_categoryid_foreign` (`categoryId`),
  KEY `race_event_teams_countryid_foreign` (`countryId`),
  CONSTRAINT `race_event_teams_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_teams_countryid_foreign` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `race_event_teams_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_teams_teamid_foreign` FOREIGN KEY (`teamId`) REFERENCES `teams` (`id`)
);

-- 14
CREATE TABLE `race_event_participants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `raceEventId` bigint unsigned NOT NULL,
  `categoryId` bigint unsigned DEFAULT NULL,
  `athleteId` bigint unsigned DEFAULT NULL,
  `attended` tinyint(1) NOT NULL DEFAULT '0',
  `disqualified` tinyint(1) NOT NULL DEFAULT '0',
  `disqualifiedText` text COLLATE utf8mb4_unicode_ci,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `countryId` bigint unsigned DEFAULT NULL,
  `raceEventTeamId` bigint unsigned DEFAULT NULL,
  `topResult` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `race_event_participants_raceeventid_foreign` (`raceEventId`),
  KEY `race_event_participants_categoryid_foreign` (`categoryId`),
  KEY `race_event_participants_athleteid_foreign` (`athleteId`),
  KEY `race_event_participants_countryid_foreign` (`countryId`),
  KEY `race_event_participants_raceeventteamid_foreign` (`raceEventTeamId`),
  CONSTRAINT `race_event_participants_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `race_event_participants_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_participants_countryid_foreign` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `race_event_participants_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_participants_raceeventteamid_foreign` FOREIGN KEY (`raceEventTeamId`) REFERENCES `race_event_teams` (`id`) ON DELETE CASCADE
);

-- 15
CREATE TABLE `race_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isTeam` tinyint(1) NOT NULL DEFAULT '0',
  `type` int NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `race_types_slug_unique` (`slug`)
);

-- 16
CREATE TABLE `race_event_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `raceEventId` bigint unsigned NOT NULL,
  `raceEventStageId` bigint unsigned DEFAULT NULL,
  `categoryId` bigint unsigned DEFAULT NULL,
  `raceEventParticipantId` bigint unsigned DEFAULT NULL,
  `raceEventTeamId` bigint unsigned DEFAULT NULL,
  `timeRaw` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` int DEFAULT NULL,
  `rank` int DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `prependTime` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `race_event_entries_raceeventid_foreign` (`raceEventId`),
  KEY `race_event_entries_categoryid_foreign` (`categoryId`),
  KEY `race_event_entries_raceeventparticipantid_foreign` (`raceEventParticipantId`),
  KEY `race_event_entries_raceeventteamid_foreign` (`raceEventTeamId`),
  KEY `race_event_entries_raceeventstageid_foreign` (`raceEventStageId`),
  CONSTRAINT `race_event_entries_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_entries_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_entries_raceeventparticipantid_foreign` FOREIGN KEY (`raceEventParticipantId`) REFERENCES `race_event_participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_entries_raceeventstageid_foreign` FOREIGN KEY (`raceEventStageId`) REFERENCES `race_event_stages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_entries_raceeventteamid_foreign` FOREIGN KEY (`raceEventTeamId`) REFERENCES `race_event_teams` (`id`) ON DELETE CASCADE
);

-- 17
CREATE TABLE `race_event_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `raceEventId` bigint unsigned DEFAULT NULL,
  `categoryId` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `race_event_categories_raceeventid_foreign` (`raceEventId`),
  KEY `race_event_categories_categoryid_foreign` (`categoryId`),
  CONSTRAINT `race_event_categories_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_categories_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE CASCADE
);

-- 18
CREATE TABLE `race_event_stages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `raceEventId` bigint unsigned NOT NULL,
  `categoryId` bigint unsigned DEFAULT NULL,
  `stage` int NOT NULL DEFAULT '1',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `race_event_stages_raceeventid_foreign` (`raceEventId`),
  KEY `race_event_stages_categoryid_foreign` (`categoryId`),
  CONSTRAINT `race_event_stages_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `race_event_stages_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE CASCADE
);

-- 19
CREATE TABLE `athlete_visits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `ipAddress` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userAgent` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `athleteId` bigint unsigned NOT NULL,
  `lastVisit` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `athlete_visits_athleteid_foreign` (`athleteId`),
  CONSTRAINT `athlete_visits_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE
);

-- 20
CREATE TABLE `athlete_top_results` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `raceEventId` bigint unsigned DEFAULT NULL,
  `athleteId` bigint unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `place` int NOT NULL DEFAULT '1',
  `race_date` timestamp NULL DEFAULT NULL,
  `position` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `athlete_top_results_athleteid_foreign` (`athleteId`),
  KEY `athlete_top_results_raceeventid_foreign` (`raceEventId`),
  CONSTRAINT `athlete_top_results_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `athlete_top_results_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE SET NULL
);

-- 21
CREATE TABLE `rankings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` int NOT NULL DEFAULT '1',
  `points` int NOT NULL DEFAULT '0',
  `athleteId` bigint unsigned NOT NULL,
  `participantId` bigint unsigned DEFAULT NULL,
  `categoryId` bigint unsigned DEFAULT NULL,
  `raceTypeId` bigint unsigned DEFAULT NULL,
  `addedBy` bigint unsigned DEFAULT NULL,
  `reason` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `obtainedAt` datetime DEFAULT NULL,
  `raceId` bigint unsigned DEFAULT NULL,
  `raceEventId` bigint unsigned DEFAULT NULL,
  `rank` int DEFAULT NULL,
  `rankingCategoryId` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rankings_athleteid_foreign` (`athleteId`),
  KEY `rankings_categoryid_foreign` (`categoryId`),
  KEY `rankings_racetypeid_foreign` (`raceTypeId`),
  KEY `rankings_addedby_foreign` (`addedBy`),
  KEY `rankings_participantid_foreign` (`participantId`),
  KEY `rankings_raceid_foreign` (`raceId`),
  KEY `rankings_raceeventid_foreign` (`raceEventId`),
  KEY `rankings_rankingcategoryid_foreign` (`rankingCategoryId`),
  CONSTRAINT `rankings_addedby_foreign` FOREIGN KEY (`addedBy`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rankings_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rankings_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rankings_participantid_foreign` FOREIGN KEY (`participantId`) REFERENCES `race_event_participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rankings_raceeventid_foreign` FOREIGN KEY (`raceEventId`) REFERENCES `race_events` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rankings_raceid_foreign` FOREIGN KEY (`raceId`) REFERENCES `races` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rankings_racetypeid_foreign` FOREIGN KEY (`raceTypeId`) REFERENCES `race_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `rankings_rankingcategoryid_foreign` FOREIGN KEY (`rankingCategoryId`) REFERENCES `ranking_categories` (`id`) ON DELETE SET NULL
);

-- 22
CREATE TABLE `ranking_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rankPointMap` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
);

-- 23
CREATE TABLE `ranking_tables` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` int unsigned NOT NULL,
  `athleteId` bigint unsigned NOT NULL,
  `rank` int unsigned NOT NULL,
  `rankBefore` int unsigned DEFAULT NULL,
  `lastRankChange` int unsigned DEFAULT NULL,
  `points` int DEFAULT NULL,
  `pointsBefore` int DEFAULT NULL,
  `dateBefore` datetime DEFAULT NULL,
  `year` int DEFAULT NULL,
  `categoryId` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ranking_tables_athleteid_foreign` (`athleteId`),
  KEY `ranking_tables_categoryid_foreign` (`categoryId`),
  CONSTRAINT `ranking_tables_athleteid_foreign` FOREIGN KEY (`athleteId`) REFERENCES `athletes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ranking_tables_categoryid_foreign` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE
);

-- 24
CREATE TABLE `partner_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci,
  `position` int NOT NULL DEFAULT '0',
  `partners` longtext COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
);
