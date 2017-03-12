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

class Groups extends Common
{
    private static $table = 'groups';
/*
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
    public static function fake($count = null)
    {
//        return self::runTest(self::$table, $count);
        return null;
    }

    public static function convert($start, $board, $count)
    {
        //
    }
}
