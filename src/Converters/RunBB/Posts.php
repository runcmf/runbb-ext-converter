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

namespace BBConverter\Converters\RunBB;

use BBConverter\Common;

class Posts extends Common
{
    private static $table = 'posts';
/*
CREATE TABLE `runbb_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `poster_id` int(10) unsigned NOT NULL DEFAULT 1,
  `poster_ip` varchar(39) DEFAULT NULL,
  `poster_email` varchar(80) DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `hide_smilies` tinyint(1) NOT NULL DEFAULT 0,
  `posted` int(10) unsigned NOT NULL DEFAULT 0,
  `edited` int(10) unsigned DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL,
  `topic_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `posts_topic_id_idx` (`topic_id`),
  KEY `posts_multi_idx` (`poster_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
    public static function fake($count = null)
    {
//        return self::runTest(self::$table, $count);
        for ($i = 1; $i <= $count; $i++) {
            $data = [
                'poster' => self::$faker->name(),
                'poster_id' => self::$faker->numberBetween(1, Info::$tables['users']['fakeCount']),
                'poster_ip' => self::$faker->ipv4(),
                'poster_email' => self::$faker->safeEmail(),
                'message' => self::$faker->text(550),
                'posted' => self::$faker->unixTime('now'),
                'topic_id' => self::$faker->numberBetween(1, Info::$tables['topics']['fakeCount'])
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

    public static function convert($start, $board, $count)
    {
        //
    }
}
