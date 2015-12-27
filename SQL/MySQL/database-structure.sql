/**
 * @author		Can Berkol
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (http://www.biberltd.com) (C) 2015
 * @license     GPLv3
 *
 * @date        10.12.2015
 */
SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for action
-- ----------------------------
DROP TABLE IF EXISTS `action`;
CREATE TABLE `action` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `code` varchar(45) COLLATE utf8_turkish_ci NOT NULL COMMENT 'A unique code that is used by the system. This code cannot be modified.',
  `date_added` datetime NOT NULL COMMENT 'Date when the action is defined.',
  `date_updated` datetime NOT NULL,
  `type` char(1) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Type of action. v:visitor, a:admin, u:user',
  `count_logs` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of actions logged.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that this action is associated with.',
  `date_removed` datetime DEFAULT NULL COMMENT 'Date when the entry is marked as removed.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUActionId` (`id`) USING BTREE,
  UNIQUE KEY `idxUActionCode` (`code`) USING BTREE,
  KEY `idxNActionDateAdded` (`date_added`),
  KEY `idxNActionType` (`type`),
  KEY `idxNActionDateUpdated` (`date_updated`),
  KEY `idxNActionDateRemoved` (`date_removed`),
  KEY `idxFSiteOfAction` (`site`) USING BTREE,
  CONSTRAINT `idxFSiteOfAction` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for action_localization
-- ----------------------------
DROP TABLE IF EXISTS `action_localization`;
CREATE TABLE `action_localization` (
  `action` int(5) unsigned NOT NULL COMMENT 'Action that is localized.',
  `language` int(5) unsigned NOT NULL COMMENT 'Language of localization.',
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localizaed name of action.',
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL COMMENT 'Localized URL key of action.',
  `description` text COLLATE utf8_turkish_ci COMMENT 'Localized description of action.',
  PRIMARY KEY (`action`,`language`),
  UNIQUE KEY `idxUActionLocalization` (`action`,`language`) USING BTREE,
  UNIQUE KEY `idxUActionUrlKey` (`action`,`url_key`,`language`) USING BTREE,
  UNIQUE KEY `idxUActionName` (`action`,`language`,`name`) USING BTREE,
  KEY `idxFActionLocalizationLanguage` (`language`) USING BTREE,
  KEY `idxFLocalizedAction` (`action`) USING BTREE,
  CONSTRAINT `idxFActionLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedAction` FOREIGN KEY (`action`) REFERENCES `action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `ip_v4` varchar(15) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'IP v4 address.',
  `ip_v6` varchar(39) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'OP V6 address if availableç',
  `url` text COLLATE utf8_turkish_ci NOT NULL COMMENT 'The URL that is visited.',
  `agent` text COLLATE utf8_turkish_ci COMMENT 'Agent / browser string.',
  `details` text COLLATE utf8_turkish_ci COMMENT 'Extra parameters. May store a serialized array.',
  `date_action` datetime NOT NULL COMMENT 'Timestamp of action.',
  `session` int(20) unsigned NOT NULL COMMENT 'Session that is being logged.',
  `action` int(5) unsigned NOT NULL COMMENT 'Action that is being logged.',
  `site` int(10) unsigned DEFAULT NULL COMMENT 'Site that log belongs to.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxULogId` (`id`) USING BTREE,
  KEY `idxFActionOfLog` (`action`) USING BTREE,
  KEY `idxFSessionOfLog` (`session`) USING BTREE,
  KEY `idxNLogDateAction` (`date_action`) USING BTREE,
  KEY `idxNLoggedActionsOfSession` (`session`,`action`) USING BTREE,
  KEY `idxNLoggedActionOfSessionInSite` (`session`,`action`,`site`) USING BTREE,
  KEY `idxFSiteOfLog` (`site`) USING BTREE,
  CONSTRAINT `idxFSiteOfLog` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFActionOfLog` FOREIGN KEY (`action`) REFERENCES `action` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFSessionOfLog` FOREIGN KEY (`session`) REFERENCES `session` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for session
-- ----------------------------
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `date_created` datetime NOT NULL COMMENT 'Date when the session is first created.',
  `date_access` datetime NOT NULL COMMENT 'Date when the session is last accessed.',
  `date_login` datetime DEFAULT NULL COMMENT 'Date when the login action is triggered.',
  `date_logout` datetime DEFAULT NULL COMMENT 'Date when the logout action is triggered.',
  `username` varchar(155) COLLATE utf8_turkish_ci DEFAULT NULL COMMENT 'If a logged in user, the username.',
  `data` text COLLATE utf8_turkish_ci COMMENT 'Session data.',
  `session_id` varchar(45) COLLATE utf8_turkish_ci NOT NULL COMMENT 'PHP session id.',
  `member` int(15) unsigned DEFAULT NULL COMMENT 'If a logged in member then which one?',
  `site` int(15) unsigned DEFAULT NULL COMMENT 'Site that session belongs to.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUSessionId` (`id`) USING BTREE,
  KEY `idx_f_session_member_idx` (`member`) USING BTREE,
  KEY `idxNSessionDateAccess` (`date_access`) USING BTREE,
  KEY `idxNSessionDateLogin` (`date_login`) USING BTREE,
  KEY `idxNSessionDateLogout` (`date_logout`) USING BTREE,
  KEY `idxNSessionDateCreated` (`date_created`) USING BTREE,
  KEY `idxFSiteOfSession` (`site`) USING BTREE,
  CONSTRAINT `idxFSiteOfSession` FOREIGN KEY (`site`) REFERENCES `site` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfSession` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci ROW_FORMAT=COMPACT;
