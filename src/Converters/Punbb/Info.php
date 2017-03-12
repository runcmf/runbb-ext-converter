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

class Info
{
    public static $name = 'PunBB';

    public static $settings = [
        'Title' => 'punBB to RunBB',
        'Forum' => 'punBB',
        'info' => '<strong>Note:</strong> passwords not converted and will be random generated.',
        'dbName' => 'punbb',
        'dbPrefix' => 'punbb_'
    ];

    /**
     * tables for convert/fake and faked data numbers suggestion
     * @var array
     */
    public static $tables = [
        'categories' => [
            'fakeCount' => 10,
            'tableTo' => 'categories'
        ],
        'censoring' => [
            'fakeCount' => 1000,
            'tableTo' => 'censoring'
        ],
        'forums' => [
            'fakeCount' => 50,
            'tableTo' => 'forums'
        ],
        'forum_perms' => [
            'fakeCount' => 90,
            'tableTo' => 'forum_perms'
        ],
        'forum_subscriptions' => [
            'fakeCount' => 40,
            'tableTo' => 'forum_subscriptions'
        ],
        'groups' => [
            'fakeCount' => 10,
            'tableTo' => 'groups'
        ],
        'posts' => [
            'fakeCount' => 200000,
            'tableTo' => 'posts'
        ],
        'subscriptions' => [
            'fakeCount' => 40,
            'tableTo' => 'topic_subscriptions'
        ],
        'topics' => [
            'fakeCount' => 50000,
            'tableTo' => 'topics'
        ],
        'users' => [
            'fakeCount' => 15000,
            'tableTo' => 'users'
        ]
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
