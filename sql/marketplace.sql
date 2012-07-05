CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`category_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `categories_plugins` (
  `category_id` varchar(32) NOT NULL,
  `plugin_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`category_id`,`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `comments` (
  `comment_id` varchar(32) NOT NULL,
  `range_id` varchar(32) NOT NULL,
  `mkdate` int(20) NOT NULL default '0',
  `comment_text` text NOT NULL,
  `user_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `dependencies` (
  `dependent_id` varchar(32) NOT NULL,
  `release_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`dependent_id`,`release_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;


CREATE TABLE IF NOT EXISTS `file_content` (
  `file_id` varchar(32) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `mkdate` int(20) NOT NULL,
  `file_content` longtext NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` int(20) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  PRIMARY KEY  (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `mp_content` (
  `content_id` varchar(32) NOT NULL,
  `content_txt` text,
  `ckey` enum('marktplatz','links','team','impressum','datenschutz','nutzungsbedingungen','faq') NOT NULL,
  PRIMARY KEY  (`content_id`),
  UNIQUE KEY `key` (`ckey`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `plugins` (
  `plugin_id` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `mkdate` int(20) NOT NULL,
  `license` varchar(255) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `in_use` text,
  `short_description` text NOT NULL,
  `release_type` varchar(255) default NULL,
  `approved` tinyint(2) NOT NULL default '0',
  `url` varchar(2000) default NULL,
  `classification` enum('firstclass','secondclass','none') NOT NULL default 'none',
  `language` enum('de','en','de_en') NOT NULL default 'de',
  PRIMARY KEY  (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `ratings` (
  `range_id` varchar(32) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `rating` int(10) NOT NULL,
  PRIMARY KEY  (`range_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `releases` (
  `release_id` varchar(32) NOT NULL,
  `plugin_id` varchar(32) NOT NULL,
  `version` varchar(255) NOT NULL,
  `studip_min_version` varchar(255) default NULL,
  `studip_max_version` varchar(255) default NULL,
  `mkdate` int(20) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `file_id` varchar(32) default NULL,
  `downloads` int(20) NOT NULL default '0',
  `release_type` varchar(255) default NULL,
  `origin` varchar(255) NOT NULL,
  PRIMARY KEY  (`release_id`),
  KEY `plugin_id` (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `rezension` (
  `rezension_id` varchar(32) NOT NULL,
  `rezension_txt` text NOT NULL,
  `user_id` varchar(32) NOT NULL,
  `mkdate` int(20) NOT NULL,
  `plugin_id` varchar(32) NOT NULL,
  KEY `plugin_id` (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `screenshots` (
  `screenshot_id` varchar(32) NOT NULL,
  `plugin_id` varchar(32) NOT NULL,
  `mkdate` int(20) NOT NULL,
  `title_screen` tinyint(2) NOT NULL default '0',
  `file_id` varchar(32) NOT NULL,
  `sort` int(20) NOT NULL default '0',
  `titel` varchar(255) default NULL,
  PRIMARY KEY  (`screenshot_id`),
  KEY `plugin_id` (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `session_data` (
  `sid` varchar(255) NOT NULL,
  `lastlogin` int(20) NOT NULL,
  `fromhost` varchar(255) NOT NULL,
  `user_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`sid`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tags` (
  `tag_id` varchar(32) NOT NULL,
  `tag` varchar(255) NOT NULL,
  PRIMARY KEY  (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `tags_objects` (
  `tag_id` varchar(32) NOT NULL,
  `object_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`tag_id`,`object_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` varchar(32) NOT NULL,
  `vorname` varchar(255) NOT NULL,
  `nachname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `passwort` varchar(255) NOT NULL,
  `salt` varchar(255) NOT NULL,
  `confirmation_token` varchar(255) NOT NULL,
  `remember_token` varchar(255) NOT NULL,
  `email_confirmed` tinyint(2) NOT NULL,
  `mkdate` int(20) NOT NULL,
  `salutation` enum('Herr','Frau') NOT NULL default 'Herr',
  `username` varchar(255) NOT NULL,
  `perm` enum('user','author','admin') NOT NULL default 'user',
  `locked` tinyint(2) NOT NULL default '0',
  'auth' varchar(30) NOT NULL default 'standard',
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users_info` (
  `user_id` varchar(32) NOT NULL,
  `url` varchar(2000) default NULL,
  `workplace` varchar(2000) default NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `user_plugins` (
  `user_id` varchar(32) NOT NULL,
  `plugin_id` varchar(32) NOT NULL,
  PRIMARY KEY  (`user_id`,`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
