<?php
/**
 * Copyright 2017 1f7.wizard@gmail.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace BBConverter\Converters\Punbb;

use BBConverter\Common;
use RunBB\Core\Random;

class Users extends Common
{
    private static $table = 'users';

    public static function fake($count = null)
    {
// difference:
//`salt` varchar(12) DEFAULT NULL,
//`facebook` varchar(100) DEFAULT NULL,
//`twitter` varchar(100) DEFAULT NULL,
//`linkedin` varchar(100) DEFAULT NULL,
//`skype` varchar(100) DEFAULT NULL,
//`access_keys` tinyint(1) NOT NULL DEFAULT 0,
//`avatar` tinyint(3) unsigned NOT NULL DEFAULT 0,
//`avatar_width` tinyint(3) unsigned NOT NULL DEFAULT 0,
//`avatar_height` tinyint(3) unsigned NOT NULL DEFAULT 0,
        $pass = Random::hash('password');
        for ($i = 1; $i <= $count; $i++) {
            $data = [
                'group_id' => self::$faker->numberBetween(2, 4),
                'username' => self::$faker->unique()->numerify(self::$faker->userName().'_######'),
                'password' => $pass,
                'email' => self::$faker->safeEmail(),
                'title' => self::$faker->company(),
                'realname' => self::$faker->name(),
                'url' => self::$faker->imageUrl(640, 480),
                'signature' => self::$faker->address()."\n\n[img]".self::$faker->imageUrl(400, 30, 'abstract').'[/img]',
                'registered' => self::$faker->unixTime('now'),
                'registration_ip' => self::$faker->ipv4(),
                'num_posts' => self::$faker->numberBetween(99, 499),
                'last_visit' => self::$faker->unixTime('now')
            ];
            self::addData(self::$table, $data);
            if($i === self::$limit) {
                $count = $count - $i;
                self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
                return $count;
            }
        }
        self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
        return null;
    }

    public static function convert($count, $tableTo)
    {
        $start = $_SESSION['convertStartFrom'] + 2;// live guest & admin as is
        $board = $_SESSION['fakeBoard'];

        $data = DB::forTable(self::$table, $board)
            ->offset($start)
            ->limit(self::$limit)
            ->orderByAsc('id')
            ->findMany();
        $i = 0;
        foreach ($data as $row) {
            $i++;
/*
CREATE TABLE `punbb_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT 3,
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `salt` varchar(12) DEFAULT NULL,
  `email` varchar(80) NOT NULL DEFAULT '',
  `title` varchar(50) DEFAULT NULL,
  `realname` varchar(40) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `facebook` varchar(100) DEFAULT NULL,
  `twitter` varchar(100) DEFAULT NULL,
  `linkedin` varchar(100) DEFAULT NULL,
  `skype` varchar(100) DEFAULT NULL,
  `jabber` varchar(80) DEFAULT NULL,
  `icq` varchar(12) DEFAULT NULL,
  `msn` varchar(80) DEFAULT NULL,
  `aim` varchar(30) DEFAULT NULL,
  `yahoo` varchar(30) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `signature` text DEFAULT NULL,
  `disp_topics` tinyint(3) unsigned DEFAULT NULL,
  `disp_posts` tinyint(3) unsigned DEFAULT NULL,
  `email_setting` tinyint(1) NOT NULL DEFAULT 1,
  `notify_with_post` tinyint(1) NOT NULL DEFAULT 0,
  `auto_notify` tinyint(1) NOT NULL DEFAULT 0,
  `show_smilies` tinyint(1) NOT NULL DEFAULT 1,
  `show_img` tinyint(1) NOT NULL DEFAULT 1,
  `show_img_sig` tinyint(1) NOT NULL DEFAULT 1,
  `show_avatars` tinyint(1) NOT NULL DEFAULT 1,
  `show_sig` tinyint(1) NOT NULL DEFAULT 1,
  `access_keys` tinyint(1) NOT NULL DEFAULT 0,
  `timezone` float NOT NULL DEFAULT 0,
  `dst` tinyint(1) NOT NULL DEFAULT 0,
  `time_format` int(10) unsigned NOT NULL DEFAULT 0,
  `date_format` int(10) unsigned NOT NULL DEFAULT 0,
  `language` varchar(25) NOT NULL DEFAULT 'English',
  `style` varchar(25) NOT NULL DEFAULT 'Oxygen',
  `num_posts` int(10) unsigned NOT NULL DEFAULT 0,
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_search` int(10) unsigned DEFAULT NULL,
  `last_email_sent` int(10) unsigned DEFAULT NULL,
  `registered` int(10) unsigned NOT NULL DEFAULT 0,
  `registration_ip` varchar(39) NOT NULL DEFAULT '0.0.0.0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_note` varchar(30) DEFAULT NULL,
  `activate_string` varchar(80) DEFAULT NULL,
  `activate_key` varchar(8) DEFAULT NULL,
  `avatar` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `avatar_width` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `avatar_height` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `punbb_users_registered_idx` (`registered`),
  KEY `punbb_users_username_idx` (`username`(8))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `runbb_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT 3,
  `username` varchar(200) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(80) NOT NULL DEFAULT '',
  `title` varchar(50) DEFAULT NULL,
  `realname` varchar(40) DEFAULT NULL,
  `url` varchar(100) DEFAULT NULL,
  `jabber` varchar(80) DEFAULT NULL,
  `icq` varchar(12) DEFAULT NULL,
  `msn` varchar(80) DEFAULT NULL,
  `aim` varchar(30) DEFAULT NULL,
  `yahoo` varchar(30) DEFAULT NULL,
  `location` varchar(30) DEFAULT NULL,
  `signature` text DEFAULT NULL,
  `disp_topics` tinyint(3) unsigned DEFAULT NULL,
  `disp_posts` tinyint(3) unsigned DEFAULT NULL,
  `email_setting` tinyint(1) NOT NULL DEFAULT 1,
  `notify_with_post` tinyint(1) NOT NULL DEFAULT 0,
  `auto_notify` tinyint(1) NOT NULL DEFAULT 0,
  `show_smilies` tinyint(1) NOT NULL DEFAULT 1,
  `show_img` tinyint(1) NOT NULL DEFAULT 1,
  `show_img_sig` tinyint(1) NOT NULL DEFAULT 1,
  `show_avatars` tinyint(1) NOT NULL DEFAULT 1,
  `show_sig` tinyint(1) NOT NULL DEFAULT 1,
  `timezone` float NOT NULL DEFAULT 0,
  `dst` tinyint(1) NOT NULL DEFAULT 0,
  `time_format` tinyint(1) NOT NULL DEFAULT 0,
  `date_format` tinyint(1) NOT NULL DEFAULT 0,
  `language` varchar(25) NOT NULL DEFAULT 'English',
  `style` varchar(25) NOT NULL DEFAULT 'runbb',
  `num_posts` int(10) unsigned NOT NULL DEFAULT 0,
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_search` int(10) unsigned DEFAULT NULL,
  `last_email_sent` int(10) unsigned DEFAULT NULL,
  `last_report_sent` int(10) unsigned DEFAULT NULL,
  `registered` int(10) unsigned NOT NULL DEFAULT 0,
  `registration_ip` varchar(39) NOT NULL DEFAULT '0.0.0.0',
  `last_visit` int(10) unsigned NOT NULL DEFAULT 0,
  `admin_note` varchar(30) DEFAULT NULL,
  `activate_string` varchar(80) DEFAULT NULL,
  `activate_key` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_idx` (`username`(25)),
  KEY `users_registered_idx` (`registered`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
            $newData = [
                'id' => $row->id,
                'group_id' => $row->group_id,
                'username' => $row->username,
                'password' => $row->password,//FIXME pass convert???
                'email' => $row->email,
                'title' => $row->title,
                'realname' => $row->realname,
                'url' => $row->url,
                'jabber' => $row->jabber,
                'icq' => $row->icq,
                'msn' => $row->msn,
                'aim' => $row->aim,
                'yahoo' => $row->yahoo,
                'location' => $row->location,
                'signature' => $row->signature,//FIXME convert BBCodes?
                'disp_topics' => $row->disp_topics,
                'disp_posts' => $row->disp_posts,
                'email_setting' => $row->email_setting,
                'notify_with_post' => $row->notify_with_post,
                'auto_notify' => $row->auto_notify,
                'show_smilies' => $row->show_smilies,
                'show_img' => $row->show_img,
                'show_img_sig' => $row->show_img_sig,
                'show_avatars' => $row->show_avatars,
                'show_sig' => $row->show_sig,
                'timezone' => $row->timezone,
                'dst' => $row->dst,
                'time_format' => $row->time_format,
                'date_format' => $row->date_format,
                'language' => $row->language,
                'num_posts' => $row->num_posts,
                'last_post' => $row->last_post,
                'last_search' => $row->last_search,
                'last_email_sent' => $row->last_email_sent,
                'registered' => $row->registered,
                'registration_ip' => $row->registration_ip,
                'last_visit' => $row->last_visit,
                'admin_note' => $row->admin_note,
                'activate_string' => $row->activate_string,
                'activate_key' => $row->activate_key
            ];
            self::insertData($tableTo, $newData);
            if($i === self::$limit) {
                $count = $count - $i;
                $_SESSION['convertStartFrom'] = $_SESSION['convertStartFrom'] + $i;
                self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
                return $count;
            }
        }
        self::pushLog(self::$table, $start, (microtime(true) - Container::get('start')));
        return null;
    }
}