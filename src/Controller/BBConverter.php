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

namespace BBConverter\Controller;

use BBConverter\Common;
use RunBB\Core\Utils;

class BBConverter extends Common
{
    public function info($req, $res, $args)
    {
        return View::setPageInfo([
            'title' => [Utils::escape(ForumSettings::get('o_board_title')), 'Converter'],
            'admin_console' => true,
            'converters' => $this->getConvertersInfo(),
        ])->display('@converter/info');
    }

    public function index($req, $res, $args)
    {
        $repair = Input::query('repair', 0);
        if($repair > 0) {
            self::repair();

            return Router::redirect(Router::pathFor('adminLogs'), ['success', 'Congratulations! All Done!']);
        }
    }

    public function display($req, $res, $args)
    {
        $board = !empty(Input::query('convertBoard', '')) ? Input::query('convertBoard') : Input::post('fakeBoard');
        $sent = Input::post('form_sent', 0);

        if($sent > 0) {
            $tables = Input::post('fakerTables');
            if(!empty($tables)) {
                // check is session set
                self::checkStorage();
                // save session
                $_SESSION['fakeBoard'] = $board;
                $_SESSION['jobTime'] = 0;
                $_SESSION['convertStartFrom'] = 0;
                $_SESSION['dbconn'] = [
                    'db_host' => Input::post('db_host', ''),
                    'db_name' => Input::post('db_name', ''),
                    'db_prefix' => Input::post('db_prefix', ''),
                    'db_type' => 'mysql',
                    'db_user' => Input::post('db_user', ''),
                    'db_pass' => Input::post('db_pass', ''),
                ];
                $this->initDBConnection($_SESSION['dbconn'], $board);
                $tables = $this->countAll($board);
                self::saveStorage($tables);
                $_SESSION['convertRows'] = $this->countRows($tables);
                $this->clearTables($tables);
                return $this->run($req, $res, $args);
            }
        }
        unset($_SESSION['fakeBoard']);
        unset($_SESSION['jobTime']);
        unset($_SESSION['convertRows']);
        unset($_SESSION['dbconn']);
        self::saveStorage([]);

        return View::setPageInfo([
            'title' => [Utils::escape(ForumSettings::get('o_board_title')), 'Converter', 'Convert'],
            'admin_console' => true,
            'board' => $board,
            'formAction' => Router::pathFor('Converter.convert.display'),
            'action' => 'Convert',
            'boardInfo' => $this->getBoardInfo($board),
        ])->display('@converter/form');
    }

    public function run($req, $res, $args)
    {
        $this->initDBConnection($_SESSION['dbconn'], $_SESSION['fakeBoard']);
        $tables = self::getStorage();

        foreach($tables as $table => $conf) {
            $class = '\\BBConverter\\Converters\\'.$_SESSION['fakeBoard'].'\\'.ucfirst(Utils::camel($table));
//            $this->truncateTable(DB::prefix().$conf['tableTo']);
            $count = $class::convert($conf['fakeCount'], $conf['tableTo']);
            if(null === $count) {
                $_SESSION['convertStartFrom'] = 0;
                unset($tables[$table]);
            } else {
                $tables[$table]['fakeCount'] = $count;
                self::saveStorage($tables);
                // redirect to himself
                return View::setPageInfo([
                    'url' => Router::pathFor('Converter.convert.run'),
                    'tables' => $tables,
                    'table' => $table,
                    'count' => $count,
                    'time' => $_SESSION['jobTime']
                ])->display('@converter/redir');
            }
        }
        $this->repair();//\ORM::DEFAULT_CONNECTION);
        Log::info('Converter finish by: '.date ('H:i:s', ceil($_SESSION['jobTime']))
            .', total rows: '.(isset($_SESSION['convertRows']) ? $_SESSION['convertRows'] : 0));
        return Router::redirect(Router::pathFor('adminLogs'), ['success', 'Congratulations! All Done!']);
    }

    // FIXME rebuild
    public static function fake($count = null){return null;}
    public static function convert($count, $tableTo){return null;}
}
