-- -----------------------------------------------------
-- Table `#__hybridauth_config`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__hybridauth_config` (
  `config_id` INT(11) NOT NULL AUTO_INCREMENT ,
  `config_name` VARCHAR(255) NOT NULL ,
  `value` TEXT NOT NULL ,
  PRIMARY KEY (`config_id`) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

-- -----------------------------------------------------
-- Table structure for table `#__hybridauth_accounts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `#__hybridauth_accounts` (
  `account_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL COMMENT 'References #__users.user_id',
  `provider_type` varchar(255) NOT NULL,
  `provider_id` varchar(255) NOT NULL,
  `websiteURL` mediumtext NOT NULL,
  `profileURL` mediumtext NOT NULL,
  `photoURL` mediumtext NOT NULL,
  `displayName` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `gender` varchar(11) NOT NULL,
  `language` varchar(255) NOT NULL,
  `age` varchar(11) NOT NULL,
  `birthDay` varchar(255) NOT NULL,
  `birthMonth` varchar(255) NOT NULL,
  `birthYear` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `country` varchar(255) NOT NULL,
  `region` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  PRIMARY KEY  (`account_id`)
) 
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;