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

class TopicsSubscriptions extends Common
{
    private static $table = 'topics_subscriptions';

    public static function fake($count = null)
    {
        return self::runTest(self::$table, $count);
    }

    public static function convert()
    {
        //
    }
}
