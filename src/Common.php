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
use Faker\Factory;

abstract class Common implements ConverterInterface
{
    protected $timer = 0;
    protected static $storage = 'BBConverter';
    public static $faker;
    public static $limit = 3000;

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
        static::$faker = Factory::create();
    }

    public function initDBConnection(array $config, $name = '')
    {
        DB::init($config, $name);
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

    /**
     * Check $_SESSION initialized
     * @return bool
     * @throws RunBBException
     */
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
        foreach ($tables as $name => $conf) {
            $this->truncateTable(DB::prefix().$conf['tableTo']);
        }
    }

    public function truncateTable($name = '')
    {
        if(false !== strpos($name, 'users')) {
            // do not delete guest adn admin
            return DB::forTable('users')
                ->whereGt('id', 2)
                ->deleteMany();
        } else {
            return DB::forTable($name)->rawExecute('TRUNCATE TABLE `' . $name . '`');
        }
    }

    protected function countAll($board)
    {
        $info = $this->getBoardInfo($board);
        $ret = [];
        foreach ($info['tables'] as $table => $conf) {
            $ret[$table] = [
                'fakeCount' => $this->countTable($board, $table),
                'tableTo' => $conf['tableTo']
            ];
        }
        return $ret;
    }

    /**
     * Count specified table records
     * @param $board
     * @param $table
     * @return mixed
     */
    public function countTable($board, $table)
    {
        return DB::forTable($table, $board)->count();
    }

    public function countRows(array & $tables)
    {
        $total = 0;
        foreach ($tables as $t => $vars) {
            $total = $total + $vars['fakeCount'];
        }
        return $total;
    }

    /**
     * Add new data to specified Forum or RunBB table
     * @param $table
     * @param array $data
     * @return mixed
     */
    public static function addData($table, array $data)
    {
        if(!empty($_SESSION['fakeBoard']) && $_SESSION['fakeBoard'] !== 'RunBB') {
            $t = DB::forTable($table, $_SESSION['fakeBoard'])->create();
        } else {
            $t = DB::forTable($table)->create();
        }
        $t->set($data);
        return $t->save();
    }

    /**
     * Insert new data to RunBB table
     * @param $table
     * @param array $data
     * @return mixed
     */
    public static function insertData($table, array $data)
    {
        $t = DB::forTable($table)->set($data)->create();
        return $t->save();
    }

    public static function pushLog($table, $count, $time)
    {
        $_SESSION['jobTime'] = $_SESSION['jobTime'] + $time;
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
            if($i === self::$limit) {
                $count = $count - $i;
                self::pushLog($table, $count, (microtime(true) - Container::get('start')));
                return $count;
            }
        }
        self::pushLog($table, $count, (microtime(true) - Container::get('start')));
        return null;
    }

    /**
     * repair forum after fill fake data
     * @param string $connName
     */
    protected function repair($connName = \ORM::DEFAULT_CONNECTION)
    {
        Repairer::forumPostSync($connName);
        Repairer::topicPostSync($connName);
        Repairer::userPostSync($connName);
        Repairer::forumLastPost($connName);
        Repairer::topicLastPost($connName);
        Repairer::deleteOrphans($connName);

        Container::get('cache')->flush();
    }
}
