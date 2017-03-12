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

class ForumPerms extends Common
{
    private static $table = 'forum_perms';

    public static function fake($count = null)
    {
        // some as RunBB
        //return \BBConverter\Converters\RunBB\ForumPerms::fake($count);
        return null;
    }

    public static function convert($count, $tableTo)
    {
        $start = $_SESSION['convertStartFrom'];
        $board = $_SESSION['fakeBoard'];

        $data = DB::forTable(self::$table, $board)
            ->offset($start)
            ->limit(self::$limit)
            ->orderByAsc('group_id')
            ->findMany();
        $i = 0;
        foreach ($data as $row) {
            $i++;
/*
CREATE TABLE `punbb_forum_perms` (
  `group_id` int(10) NOT NULL DEFAULT 0,
  `forum_id` int(10) NOT NULL DEFAULT 0,
  `read_forum` tinyint(1) NOT NULL DEFAULT 1,
  `post_replies` tinyint(1) NOT NULL DEFAULT 1,
  `post_topics` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`group_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `runbb_forum_perms` (
  `group_id` int(10) NOT NULL DEFAULT 0,
  `forum_id` int(10) NOT NULL DEFAULT 0,
  `read_forum` tinyint(1) NOT NULL DEFAULT 1,
  `post_replies` tinyint(1) NOT NULL DEFAULT 1,
  `post_topics` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`group_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
            $newData = [
                'group_id' => $row->group_id,
                'forum_id' => $row->forum_id,
                'read_forum' => $row->read_forum,
                'post_replies' => $row->post_replies,
                'post_topics' => $row->post_topics,
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