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

class Categories extends Common
{
    private static $table = 'categories';

    public static function fake($count)
    {
        // some as RunBB
        return \BBConverter\Converters\RunBB\Categories::fake($count);
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
CREATE TABLE `punbb_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(80) NOT NULL DEFAULT 'New Category',
  `disp_position` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
/*
CREATE TABLE `runbb_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(80) NOT NULL DEFAULT 'New Category',
  `disp_position` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
*/
            $newData = [
                'id' => $row->id,
                'cat_name' => $row->cat_name,
                'disp_position' => $row->disp_position
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
