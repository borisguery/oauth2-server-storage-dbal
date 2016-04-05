-- Create syntax for TABLE 'oauth2_access_tokens'
CREATE TABLE `oauth2_access_tokens` (
  `access_token`        VARCHAR(255) NOT NULL DEFAULT '',
  `client_id`           VARCHAR(255) NOT NULL,
  `expires_at`          DATETIME     NOT NULL,
  `scopes`              TEXT         NOT NULL,
  `resource_owner_id`   VARCHAR(255) NOT NULL,
  `resource_owner_type` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`access_token`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `oauth2_access_tokens_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `oauth2_clients` (`client_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- Create syntax for TABLE 'oauth2_clients'
CREATE TABLE `oauth2_clients` (
  `client_id`           VARCHAR(255) NOT NULL DEFAULT '',
  `client_secret`       VARCHAR(255) NOT NULL,
  `redirect_uris`       TEXT         NOT NULL,
  `allowed_grant_types` TEXT         NOT NULL,
  PRIMARY KEY (`client_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

-- Create syntax for TABLE 'oauth2_refresh_tokens'
CREATE TABLE `oauth2_refresh_tokens` (
  `refresh_token`           VARCHAR(255) NOT NULL DEFAULT '',
  `associated_access_token` VARCHAR(255) NOT NULL,
  `expires_at`              DATETIME     NOT NULL,
  PRIMARY KEY (`refresh_token`),
  KEY `associated_access_token` (`associated_access_token`),
  CONSTRAINT `oauth2_refresh_tokens_ibfk_1` FOREIGN KEY (`associated_access_token`) REFERENCES `oauth2_access_tokens` (`access_token`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
