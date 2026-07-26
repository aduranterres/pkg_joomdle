 CREATE TABLE IF NOT EXISTS `#__joomdle_sso_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_hash` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` bigint DEFAULT NULL,
  `consumed` bigint DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_joomdle_sso_tickets_token_hash` (`token_hash`),
  KEY `idx_joomdle_sso_tickets_created` (`created`)
);
