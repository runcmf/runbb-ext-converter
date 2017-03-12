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

class Topics extends Common
{
    private static $table = 'topics';
/*
CREATE TABLE `runbb_topics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `posted` int(10) unsigned NOT NULL DEFAULT 0,
  `first_post_id` int(10) unsigned NOT NULL DEFAULT 0,
  `last_post` int(10) unsigned NOT NULL DEFAULT 0,
  `last_post_id` int(10) unsigned NOT NULL DEFAULT 0,
  `last_poster` varchar(200) DEFAULT NULL,
  `num_views` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_replies` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `closed` tinyint(1) NOT NULL DEFAULT 0,
  `sticky` tinyint(1) NOT NULL DEFAULT 0,
  `moved_to` int(10) unsigned DEFAULT NULL,
  `forum_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `topics_forum_id_idx` (`forum_id`),
  KEY `topics_moved_to_idx` (`moved_to`),
  KEY `topics_last_post_idx` (`last_post`),
  KEY `topics_first_post_id_idx` (`first_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
    public static function fake($count = null)
    {
//        return self::runTest(self::$table, $count);
        for ($i = 1; $i <= $count; $i++) {
            $data = [
                'poster' => self::$faker->name(),
                'subject' => self::$faker->text(80),
                'posted' => self::$faker->unixTime('now'),
                'last_poster' => self::$faker->numberBetween(2, Info::$tables['users']['fakeCount']),
                'num_views' => self::$faker->numberBetween(5, 999),
                'forum_id' => self::$faker->numberBetween(1, Info::$tables['forums']['fakeCount'])
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
