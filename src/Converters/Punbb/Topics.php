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

class Topics extends Common
{
    private static $table = 'topics';

    public static function fake($count = null)
    {
        // some as RunBB
        return \BBConverter\Converters\RunBB\Topics::fake($count);
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
CREATE TABLE `punbb_topics` (
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
  KEY `punbb_topics_forum_id_idx` (`forum_id`),
  KEY `punbb_topics_moved_to_idx` (`moved_to`),
  KEY `punbb_topics_last_post_idx` (`last_post`),
  KEY `punbb_topics_first_post_id_idx` (`first_post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
            $newData = [
                'id' => $row->id,
                'poster' => $row->poster,
                'subject' => $row->subject,
                'posted' => $row->posted,
                'first_post_id' => $row->first_post_id,
                'last_post' => $row->last_post,
                'last_post_id' => $row->last_post_id,
                'last_poster' => $row->last_poster,
                'num_views' => $row->num_views,
                'num_replies' => $row->num_replies,
                'closed' => $row->closed,
                'sticky' => $row->sticky,
                'moved_to' => $row->moved_to,
                'forum_id' => $row->forum_id
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