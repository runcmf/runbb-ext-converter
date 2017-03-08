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

class Info
{
    public static $settings = [
        'Title' => 'RunBB for fake',
        'Forum' => 'RunBB',
        'info' => '<strong>Note:</strong> some note will be here.',
        'dbName' => 'fromConfig',
        'dbPrefix' => ORM_TABLE_PREFIX
    ];

    /**
     * tables for convert/fake and faked data numbers suggestion
     * @var array
     */
    public static $tables = [
        'categories' => 25,
        'censoring' => 1000,
        'forums' => 500,
        'forum_perms' => 100,
        'forum_subscriptions' => 500,
        'posts' => 200000,
        'topics' => 50000,
        'topic_subscriptions' => 500,
        'users' => 15000,
    ];

    public static function getGroups()
    {
        return [
            0 => ForumEnv::get('FEATHER_UNVERIFIED'), // Not Active
            1 => ForumEnv::get('FEATHER_ADMIN'), // Admins
            2 => ForumEnv::get('FEATHER_GUEST'), // Guests
            3 => ForumEnv::get('FEATHER_MEMBER'), // Registered
            4 => ForumEnv::get('FEATHER_MOD'), // Moderators
        ];
    }
}
