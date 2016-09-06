<?php
namespace Utility;
class DatabaseCreateScript implements \Iterator
{
    private $key;
    public static $statements = array(
<<<EOT
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
EOT
    ,
<<<EOT
CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `time_start` datetime NOT NULL,
  `time_end` datetime NOT NULL,
  `notes` varchar(1024) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `creator_id` int(11) NOT NULL,
  `chain` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `submitted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
    ,
<<<EOT
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int(11) NOT NULL,
  `login` varchar(64) COLLATE utf8_bin NOT NULL,
  `email` varchar(129) COLLATE utf8_bin NOT NULL,
  `pwd_hash` varchar(40) COLLATE utf8_bin DEFAULT NULL,
  `is_admin` tinyint(4) NOT NULL DEFAULT '0',
  `hour_mode` tinyint(4) NOT NULL DEFAULT '24',
  `first_day` tinyint(4) NOT NULL DEFAULT '0',
  `name` varchar(128) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
    ,
<<<EOT
CREATE TABLE IF NOT EXISTS `rooms` (
`id` int(11) NOT NULL,
`room_name` varchar(64) COLLATE utf8_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
    ,
<<<EOT
ALTER TABLE `appointments`
ADD PRIMARY KEY (`id`),
ADD KEY `time_start` (`time_start`),
ADD KEY `chain` (`chain`)
EOT
    ,
<<<EOT
ALTER TABLE `employees`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `login` (`login`)
EOT
    ,
<<<EOT
ALTER TABLE `rooms`
ADD PRIMARY KEY (`id`)
EOT
    ,
<<<EOT
ALTER TABLE `appointments`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT
EOT
    ,
<<<EOT
ALTER TABLE `employees`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT
EOT
    ,
<<<EOT
ALTER TABLE `rooms`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT
EOT
    ,
<<<EOT
INSERT INTO `employees`(`login`, `is_admin`) VALUES ('admin',1)
EOT
    ,
<<<EOT
insert into rooms (`room_name`) values ('Boardroom 1')
EOT
    ,
<<<EOT
insert into rooms (`room_name`) values ('Boardroom 2')
EOT
    ,
<<<EOT
insert into rooms (`room_name`) values ('Boardroom 3')
EOT
    );

    /**
     * DatabaseCreateScript constructor.
     */
    public function __construct()
    {
        $this->rewind();
    }

    public function current()
    {
        return self::$statements[$this->key];
    }

    public function next()
    {
        $this->key++;
    }

    public function key()
    {
        return $this->key;
    }

    public function valid()
    {
        return isset(self::$statements[$this->key]);
    }

    public function rewind()
    {
        $this->key = 0;
    }
}