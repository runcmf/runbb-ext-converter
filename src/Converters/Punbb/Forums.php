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

class Forums extends Common
{
    private static $table = 'forums';

    public static function fake($count = null)
    {
        // some as RunBB
        return \BBConverter\Converters\RunBB\Forums::fake($count);
    }

    public static function convert($count, $tableTo)
    {
        $start = $_SESSION['convertStartFrom'];
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
CREATE TABLE `punbb_forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(80) NOT NULL DEFAULT 'New forum',
  `forum_desc` text DEFAULT NULL,
  `redirect_url` varchar(100) DEFAULT NULL,
  `moderators` text DEFAULT NULL,
  `num_topics` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_posts` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_post_id` int(10) unsigned DEFAULT NULL,
  `last_poster` varchar(200) DEFAULT NULL,
  `sort_by` tinyint(1) NOT NULL DEFAULT 0,
  `disp_position` int(10) NOT NULL DEFAULT 0,
  `cat_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `runbb_forums` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `forum_name` varchar(80) NOT NULL DEFAULT 'New forum',
  `forum_desc` text DEFAULT NULL,
  `redirect_url` varchar(100) DEFAULT NULL,
  `moderators` text DEFAULT NULL,
  `num_topics` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `num_posts` mediumint(8) unsigned NOT NULL DEFAULT 0,
  `last_post` int(10) unsigned DEFAULT NULL,
  `last_post_id` int(10) unsigned DEFAULT NULL,
  `last_poster` varchar(200) DEFAULT NULL,
  `sort_by` tinyint(1) NOT NULL DEFAULT 0,
  `disp_position` int(10) NOT NULL DEFAULT 0,
  `cat_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
            $newData = [
                'id' => $row->id,
                'forum_name' => $row->forum_name,
                'forum_desc' => $row->forum_desc,
                'redirect_url' => $row->redirect_url,
                'moderators' => $row->moderators,
                'num_topics' => $row->num_topics,
                'num_posts' => $row->num_posts,
                'last_post' => $row->last_post,
                'last_post_id' => $row->last_post_id,
                'last_poster' => $row->last_poster,
                'sort_by' => $row->sort_by,
                'disp_position' => $row->disp_position,
                'cat_id' => $row->cat_id
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