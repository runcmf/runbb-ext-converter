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

class Posts extends Common
{
    private static $table = 'posts';

    public static function fake($count = null)
    {
        // some as RunBB. only message length differences
        return \BBConverter\Converters\RunBB\Posts::fake($count);
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
CREATE TABLE `punbb_posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `poster` varchar(200) NOT NULL DEFAULT '',
  `poster_id` int(10) unsigned NOT NULL DEFAULT 1,
  `poster_ip` varchar(39) DEFAULT NULL,
  `poster_email` varchar(80) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `hide_smilies` tinyint(1) NOT NULL DEFAULT 0,
  `posted` int(10) unsigned NOT NULL DEFAULT 0,
  `edited` int(10) unsigned DEFAULT NULL,
  `edited_by` varchar(200) DEFAULT NULL,
  `topic_id` int(10) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `punbb_posts_topic_id_idx` (`topic_id`),
  KEY `punbb_posts_multi_idx` (`poster_id`,`topic_id`),
  KEY `punbb_posts_posted_idx` (`posted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
            $newData = [
                'id' => $row->id,
                'poster' => $row->poster,
                'poster_id' => $row->poster_id,
                'poster_ip' => $row->poster_ip,
                'poster_email' => $row->poster_email,
                'message' => $row->message,//FIXME convert BBCodes
                'hide_smilies' => $row->hide_smilies,
                'posted' => $row->posted,
                'edited' => $row->edited,
                'edited_by' => $row->edited_by,
                'topic_id' => $row->topic_id
            ];
            self::insertData($tableTo, $newData);
            if($i === self::$limit) {
                $count = $count - $i;
                $_SESSION['convertStartFrom'] = $_SESSION['convertStartFrom'] + $i;
                self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
                return $count;
            }
        }
        self::pushLog(self::$table, $count, (microtime(true) - Container::get('start')));
        return null;
    }
}