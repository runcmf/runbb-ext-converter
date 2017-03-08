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

namespace BBConverter;

/**
 * Class Repairer
 * @package BBConverter
 */
class Repairer
{
    /**
     * synchronise forum posts
     */
    public static function forumPostSync()
    {
        $begin = microtime(true);
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_posts 
            SELECT t.forum_id, count(*) as posts FROM '.ORM_TABLE_PREFIX.'posts as p 
            LEFT JOIN '.ORM_TABLE_PREFIX.'topics as t on p.topic_id=t.id GROUP BY t.forum_id');
        \ORM::rawExecute('UPDATE '.ORM_TABLE_PREFIX.'forums, '.ORM_TABLE_PREFIX.'tmp_posts 
            SET num_posts=posts WHERE id=forum_id');
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_topics 
            SELECT forum_id, count(*) as topics FROM '.ORM_TABLE_PREFIX.'topics GROUP BY forum_id');
        \ORM::rawExecute('UPDATE '.ORM_TABLE_PREFIX.'forums, '.ORM_TABLE_PREFIX.'tmp_topics 
            SET num_topics=topics WHERE id=forum_id');

        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin));
    }

    /**
     * synchronise topic posts
     */
    public static function topicPostSync()
    {
        $begin = microtime(true);
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_topic_posts 
            SELECT topic_id, count(*)-1 as replies FROM '.ORM_TABLE_PREFIX.'posts GROUP BY topic_id');
        \ORM::rawExecute('UPDATE '.ORM_TABLE_PREFIX.'topics, '.ORM_TABLE_PREFIX.'tmp_topic_posts 
            SET num_replies=replies WHERE id=topic_id');

        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin));
    }

    /**
     * synchronise user posts
     */
    public static function userPostSync()
    {
        $begin = microtime(true);
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_user_posts 
            SELECT poster_id, count(*) as posts FROM '.ORM_TABLE_PREFIX.'posts GROUP BY poster_id');
        \ORM::rawExecute('UPDATE '.ORM_TABLE_PREFIX.'users, '.ORM_TABLE_PREFIX.'tmp_user_posts SET num_posts=posts 
            WHERE id=poster_id');

        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin));
    }

    /**
     * synchronise forum last posts
     */
    public static function forumLastPost()
    {
        $begin = microtime(true);
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_last 
            SELECT p.posted AS n_last_post, p.id AS n_last_post_id, p.poster AS n_last_poster, t.forum_id 
            FROM '.ORM_TABLE_PREFIX.'posts AS p 
            LEFT JOIN '.ORM_TABLE_PREFIX.'topics AS t ON p.topic_id=t.id ORDER BY p.posted DESC');
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_lastb 
            SELECT * FROM '.ORM_TABLE_PREFIX.'tmp_last WHERE forum_id > 0 GROUP BY forum_id');
        \ORM::rawExecute('UPDATE '.ORM_TABLE_PREFIX.'forums, '.ORM_TABLE_PREFIX.'tmp_lastb 
            SET last_post_id=n_last_post_id, last_post=n_last_post, last_poster=n_last_poster WHERE id=forum_id');

        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin));
    }

    /**
     * synchronise topic last posts
     */
    public static function topicLastPost()
    {
        $begin = microtime(true);
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_topic_last 
            SELECT posted AS n_last_post, id AS n_last_post_id, poster AS n_last_poster, topic_id 
            FROM '.ORM_TABLE_PREFIX.'posts ORDER BY posted DESC');
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_topic_lastb 
            SELECT * FROM '.ORM_TABLE_PREFIX.'tmp_topic_last WHERE topic_id > 0 GROUP BY topic_id');
        \ORM::rawExecute('UPDATE '.ORM_TABLE_PREFIX.'topics, '.ORM_TABLE_PREFIX.'tmp_topic_lastb 
            SET last_post_id=n_last_post_id, last_post=n_last_post, last_poster=n_last_poster WHERE id=topic_id');

        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin));
    }

    /**
     * Clear orphans
     */
    public static function deleteOrphans()
    {
        $begin = microtime(true);
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_orph_topic 
            SELECT t.id as o_id FROM '.ORM_TABLE_PREFIX.'topics AS t 
            LEFT JOIN '.ORM_TABLE_PREFIX.'posts AS p ON p.topic_id = t.id WHERE p.id IS NULL');
        \ORM::rawExecute('DELETE '.ORM_TABLE_PREFIX.'topics FROM '.ORM_TABLE_PREFIX.'topics, 
            '.ORM_TABLE_PREFIX.'tmp_orph_topic WHERE o_id=id');
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_orph_posts 
            SELECT p.id as o_id FROM '.ORM_TABLE_PREFIX.'posts p 
            LEFT JOIN '.ORM_TABLE_PREFIX.'topics t ON p.topic_id=t.id WHERE t.id IS NULL');
        \ORM::rawExecute('DELETE '.ORM_TABLE_PREFIX.'posts FROM '.ORM_TABLE_PREFIX.'posts, 
            '.ORM_TABLE_PREFIX.'tmp_orph_posts WHERE o_id=id');
        \ORM::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.ORM_TABLE_PREFIX.'tmp_orph_topics 
            SELECT t.id as o_id FROM '.ORM_TABLE_PREFIX.'topics as t 
            LEFT JOIN '.ORM_TABLE_PREFIX.'forums as f ON t.forum_id=f.id WHERE f.id is NULL');
        \ORM::rawExecute('DELETE '.ORM_TABLE_PREFIX.'topics FROM '.ORM_TABLE_PREFIX.'topics, 
            '.ORM_TABLE_PREFIX.'tmp_orph_topics WHERE o_id=id');

        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin));
    }
}