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

class ForumPerms extends Common
{
    private static $table = 'forum_perms';
/*
CREATE TABLE `runbb_forum_perms` (
  `group_id` int(10) NOT NULL DEFAULT 0,
  `forum_id` int(10) NOT NULL DEFAULT 0,
  `read_forum` tinyint(1) NOT NULL DEFAULT 1,
  `post_replies` tinyint(1) NOT NULL DEFAULT 1,
  `post_topics` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`group_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

 */
    public static function fake($count = null)
    {
//        return self::runTest(self::$table, $count);
        for ($i = 1; $i <= $count; $i++) {
            $data = [
                'group_id' => self::$faker->numberBetween(1, 6),
                'forum_id' => $i
            ];
            self::addData(ORM_TABLE_PREFIX.self::$table, $data);
            if($i === self::$limit) {
                $count = $count - $i;
                self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
                return $count;
            }
        }
        self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
        return null;
    }

    public static function convert()
    {
        //
    }
}
