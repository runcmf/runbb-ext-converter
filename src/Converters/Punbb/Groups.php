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

class Groups extends Common
{
    private static $table = 'groups';

    public static function fake($count = null)
    {
        // no fake to groups
        return null;
    }

    public static function convert($count, $tableTo)
    {
        $start = $_SESSION['convertStartFrom'];
        $board = $_SESSION['fakeBoard'];

        $data = DB::forTable(self::$table, $board)
            ->offset($start)
            ->limit(self::$limit)
            ->orderByAsc('g_id')
            ->findMany();
        $i = 0;
        foreach ($data as $row) {
            $i++;
/*
CREATE TABLE `punbb_groups` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(50) NOT NULL DEFAULT '',
  `g_user_title` varchar(50) DEFAULT NULL,
  `g_moderator` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_edit_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_rename_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_change_passwords` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_ban_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_read_board` tinyint(1) NOT NULL DEFAULT 1,
  `g_view_users` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_replies` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_topics` tinyint(1) NOT NULL DEFAULT 1,
  `g_edit_posts` tinyint(1) NOT NULL DEFAULT 1,
  `g_delete_posts` tinyint(1) NOT NULL DEFAULT 1,
  `g_delete_topics` tinyint(1) NOT NULL DEFAULT 1,
  `g_set_title` tinyint(1) NOT NULL DEFAULT 1,
  `g_search` tinyint(1) NOT NULL DEFAULT 1,
  `g_search_users` tinyint(1) NOT NULL DEFAULT 1,
  `g_send_email` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_flood` smallint(6) NOT NULL DEFAULT 30,
  `g_search_flood` smallint(6) NOT NULL DEFAULT 30,
  `g_email_flood` smallint(6) NOT NULL DEFAULT 60,
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `runbb_groups` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(50) NOT NULL DEFAULT '',
  `g_user_title` varchar(50) DEFAULT NULL,
  `g_promote_min_posts` int(10) unsigned NOT NULL DEFAULT 0,
  `g_promote_next_group` int(10) unsigned NOT NULL DEFAULT 0,
  `g_moderator` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_edit_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_rename_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_change_passwords` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_ban_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_mod_promote_users` tinyint(1) NOT NULL DEFAULT 0,
  `g_read_board` tinyint(1) NOT NULL DEFAULT 1,
  `g_view_users` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_replies` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_topics` tinyint(1) NOT NULL DEFAULT 1,
  `g_edit_posts` tinyint(1) NOT NULL DEFAULT 1,
  `g_delete_posts` tinyint(1) NOT NULL DEFAULT 1,
  `g_delete_topics` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_links` tinyint(1) NOT NULL DEFAULT 1,
  `g_set_title` tinyint(1) NOT NULL DEFAULT 1,
  `g_search` tinyint(1) NOT NULL DEFAULT 1,
  `g_search_users` tinyint(1) NOT NULL DEFAULT 1,
  `g_send_email` tinyint(1) NOT NULL DEFAULT 1,
  `g_post_flood` smallint(6) NOT NULL DEFAULT 30,
  `g_search_flood` smallint(6) NOT NULL DEFAULT 30,
  `g_email_flood` smallint(6) NOT NULL DEFAULT 60,
  `g_report_flood` smallint(6) NOT NULL DEFAULT 60,
  `g_parser_plugins` text DEFAULT NULL,
  `inherit` text DEFAULT NULL,
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

*/
            $newData = [
                'g_id' => $row->g_id,
                'g_title' => $row->g_title,
                'g_user_title' => $row->g_user_title,
                'g_moderator' => $row->g_moderator,
                'g_mod_edit_users' => $row->g_mod_edit_users,
                'g_mod_rename_users' => $row->g_mod_rename_users,
                'g_mod_change_passwords' => $row->g_mod_change_passwords,
                'g_mod_ban_users' => $row->g_mod_ban_users,
                'g_read_board' => $row->g_read_board,
                'g_view_users' => $row->g_view_users,
                'g_post_replies' => $row->g_post_replies,
                'g_post_topics' => $row->g_post_topics,
                'g_edit_posts' => $row->g_edit_posts,
                'g_delete_posts' => $row->g_delete_posts,
                'g_delete_topics' => $row->g_delete_topics,
                'g_set_title' => $row->g_set_title,
                'g_search' => $row->g_search,
                'g_search_users' => $row->g_search_users,
                'g_send_email' => $row->g_send_email,
                'g_post_flood' => $row->g_post_flood,
                'g_search_flood' => $row->g_search_flood,
                'g_email_flood' => $row->g_email_flood
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