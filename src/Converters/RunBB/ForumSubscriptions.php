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

class ForumSubscriptions extends Common
{
    private static $table = 'forum_subscriptions';
/*
CREATE TABLE `runbb_forum_subscriptions` (
  `user_id` int(10) unsigned NOT NULL DEFAULT 0,
  `forum_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`user_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
    public static function fake($count = null)
    {
//        return self::runTest(self::$table, $count);
        for ($i = 1; $i <= $count; $i++) {
            $data = [
                'user_id' => self::$faker->unique()->numberBetween(1, Info::$tables['users']['fakeCount']),
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
