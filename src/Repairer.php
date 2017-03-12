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
     * synchronize forum posts
     */
    public static function forumPostSync($connName = \ORM::DEFAULT_CONNECTION)
    {
        $begin = microtime(true);
        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_posts 
            SELECT t.forum_id, count(*) as posts FROM '.DB::prefix($connName).'posts as p 
            LEFT JOIN '.DB::prefix($connName).'topics as t on p.topic_id=t.id GROUP BY t.forum_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('UPDATE '.DB::prefix($connName).'forums, '.DB::prefix($connName).'tmp_posts 
            SET num_posts=posts WHERE id=forum_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_topics 
            SELECT forum_id, count(*) as topics FROM '.DB::prefix($connName).'topics GROUP BY forum_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('UPDATE '.DB::prefix($connName).'forums, '.DB::prefix($connName).'tmp_topics 
            SET num_topics=topics WHERE id=forum_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);
    }

    /**
     * synchronize topic posts
     */
    public static function topicPostSync($connName = \ORM::DEFAULT_CONNECTION)
    {
        $begin = microtime(true);
        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_topic_posts 
            SELECT topic_id, count(*)-1 as replies FROM '.DB::prefix($connName).'posts GROUP BY topic_id',
            [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('UPDATE '.DB::prefix($connName).'topics, '.DB::prefix($connName).'tmp_topic_posts 
            SET num_replies=replies WHERE id=topic_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);
    }

    /**
     * synchronize user posts
     */
    public static function userPostSync($connName = \ORM::DEFAULT_CONNECTION)
    {
        $begin = microtime(true);
        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_user_posts 
            SELECT poster_id, count(*) as posts FROM '.DB::prefix($connName).'posts GROUP BY poster_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('UPDATE '.DB::prefix($connName).'users, '.DB::prefix($connName).
            'tmp_user_posts SET num_posts=posts WHERE id=poster_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);
    }

    /**
     * synchronize forum last posts
     */
    public static function forumLastPost($connName = \ORM::DEFAULT_CONNECTION)
    {
        $begin = microtime(true);
        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_last 
            SELECT p.posted AS n_last_post, p.id AS n_last_post_id, p.poster AS n_last_poster, t.forum_id 
            FROM '.DB::prefix($connName).'posts AS p 
            LEFT JOIN '.DB::prefix($connName).'topics AS t ON p.topic_id=t.id ORDER BY p.posted DESC', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_lastb 
            SELECT * FROM '.DB::prefix($connName).'tmp_last WHERE forum_id > 0 GROUP BY forum_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('UPDATE '.DB::prefix($connName).'forums, '.DB::prefix($connName).'tmp_lastb 
            SET last_post_id=n_last_post_id, last_post=n_last_post, last_poster=n_last_poster WHERE id=forum_id',
            [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);
    }

    /**
     * synchronize topic last posts
     */
    public static function topicLastPost($connName = \ORM::DEFAULT_CONNECTION)
    {
        $begin = microtime(true);
        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_topic_last 
            SELECT posted AS n_last_post, id AS n_last_post_id, poster AS n_last_poster, topic_id 
            FROM '.DB::prefix($connName).'posts ORDER BY posted DESC', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_topic_lastb 
            SELECT * FROM '.DB::prefix($connName).'tmp_topic_last WHERE topic_id > 0 GROUP BY topic_id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('UPDATE '.DB::prefix($connName).'topics, '.DB::prefix($connName).'tmp_topic_lastb 
            SET last_post_id=n_last_post_id, last_post=n_last_post, last_poster=n_last_poster WHERE id=topic_id',
            [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);
    }

    /**
     * Clear orphans
     */
    public static function deleteOrphans($connName = \ORM::DEFAULT_CONNECTION)
    {
        $begin = microtime(true);
        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_orph_topic 
            SELECT t.id as o_id FROM '.DB::prefix($connName).'topics AS t 
            LEFT JOIN '.DB::prefix($connName).'posts AS p ON p.topic_id = t.id WHERE p.id IS NULL', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('DELETE '.DB::prefix($connName).'topics FROM '.DB::prefix($connName).'topics, 
            '.DB::prefix($connName).'tmp_orph_topic WHERE o_id=id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_orph_posts 
            SELECT p.id as o_id FROM '.DB::prefix($connName).'posts p 
            LEFT JOIN '.DB::prefix($connName).'topics t ON p.topic_id=t.id WHERE t.id IS NULL', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('DELETE '.DB::prefix($connName).'posts FROM '.DB::prefix($connName).'posts, 
            '.DB::prefix($connName).'tmp_orph_posts WHERE o_id=id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('CREATE TEMPORARY TABLE IF NOT EXISTS '.DB::prefix($connName).'tmp_orph_topics 
            SELECT t.id as o_id FROM '.DB::prefix($connName).'topics as t 
            LEFT JOIN '.DB::prefix($connName).'forums as f ON t.forum_id=f.id WHERE f.id is NULL', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);

        $ret = DB::rawExecute('DELETE '.DB::prefix($connName).'topics FROM '.DB::prefix($connName).'topics, 
            '.DB::prefix($connName).'tmp_orph_topics WHERE o_id=id', [], $connName);
        Log::info('REPAIR '.__METHOD__.', by: '.(microtime(true) - $begin).', retval: '.$ret);
    }
}
