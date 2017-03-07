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

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RunBB\Core\AdminUtils;
use RunBB\Exception\RunBBException;

class Common
{
    protected $timer = 0;
    protected static $storage = 'BBConverter';

    public function __construct()
    {
        $this->timer = microtime(true);
        header('Content-type: text/html; charset=utf-8');
        @set_time_limit(0);
        @ini_set('display_errors', true);
        @ini_set('memory_limit', -1);

        Lang::load('converter-index', 'converter', dirname(__FILE__) . '/Lang');
        View::addTemplatesDirectory(dirname(__FILE__) . '/Views', 'converter');
        $this->crumbs = [
            Router::pathFor('Converter.home') => 'Converter Index'
        ];
        AdminUtils::generateAdminMenu('admin-converter');
    }

    public function getConvertersInfo($dir=false)
    {
        $dir = !$dir ? __DIR__ : $dir;
        $dir = $dir . '/Converters';

        $rdi = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
        $rii = new RecursiveIteratorIterator($rdi);
        $files = [];
        foreach($rii as $object){
            if ($object->getFileName() === 'Info.php') {
                $class = 'BBConverter\\Converters\\'.basename($object->getPath()).'\\Info';
                $files[] = [
                    'name' => basename($object->getPath()),
                    'file' => $object->getFileName(),
                    'info' => $class::$settings,
                    'tables' => $class::$tables
                ];
            }
        }
        return $files;
    }

    public function getBoardInfo($board = '')
    {
        $class = 'BBConverter\\Converters\\'.$board.'\\Info';
        if (class_exists($class)) {
            return [
                'info' => $class::$settings,
                'tables' => $class::$tables
            ];
        }
        return [];
    }

    public static function checkStorage()
    {
        if (!isset($_SESSION)) {
            throw new RunBBException('Session not initialised.');
        }
        return true;
    }

    public static function saveStorage(array $data)
    {
        return $_SESSION[self::$storage] = json_encode($data);
    }

    public static function getStorage()
    {
        return json_decode($_SESSION[self::$storage], true);
    }

    public function clearTables(array $tables)
    {
        foreach ($tables as $name => $count) {
            $this->truncateTable(ORM_TABLE_PREFIX.$name);
        }
    }

    public function truncateTable($name = '')
    {
        return \ORM::forTable($name)->rawExecute('TRUNCATE TABLE `'.$name.'`');
    }

    public function countRows(array & $tables)
    {
        $total = 0;
        foreach ($tables as $t => $count) {
            $total = $total + $count;
        }
        return $total;
    }

    public static function addData($table, array $data)
    {
        $t = \ORM::forTable($table)->create();
        $t->set($data);
        return $t->save();
    }

    private static function pushLog($table, $count, $time)
    {
        $_SESSION['fakeTime'] = $_SESSION['fakeTime'] + $time;
        Log::info('fill table: '.$table.', with count to end: '.$count.', by: '.$time);
    }

    /**
     * Заглушка для теста
     * @param string $table
     * @param null $count
     * @return null
     */
    public static function runTest($table = 'notSet', $count=null)
    {
        for ($i = 1; $i <= $count; $i++) {
            usleep(2000);// 2 mill for emulate query
            if($i === 3000) {
                $count = $count - $i;
                self::pushLog($table, $count, (microtime(true) - Container::get('start')));
                return $count;
            }
        }
        self::pushLog($table, $count, (microtime(true) - Container::get('start')));
        return null;
    }
}
