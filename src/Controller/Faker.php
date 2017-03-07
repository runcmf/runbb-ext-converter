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

class Faker extends Common
{
    public function display($req, $res, $args)
    {
        $board = !empty(Input::query('fakeBoard', '')) ? Input::query('fakeBoard') : Input::post('fakeBoard');
        $sent = Input::post('form_sent', 0);

        if($sent > 0) {
            $fakerTables = Input::post('fakerTables');
            if(!empty($fakerTables)) {
                // check is session set
                self::checkStorage();
                // save session
                $_SESSION['fakeBoard'] = $board;
                $_SESSION['fakeTime'] = 0;
                $_SESSION['fakeRows'] = $this->countRows($fakerTables);
                self::saveStorage($fakerTables);

                //$this->clearTables($fakerTables);
                return $this->run($req, $res, $args);
            }
        }
        unset($_SESSION['fakerBoard']);
        unset($_SESSION['fakeTime']);
        unset($_SESSION['fakeRows']);
        self::saveStorage([]);

        return View::setPageInfo([
            'title' => [Utils::escape(ForumSettings::get('o_board_title')), 'Converter'],
            'admin_console' => true,
            'board' => $board,
            'boardInfo' => $this->getBoardInfo($board),
            'providers' => $this->getProviders()
        ])->display('@converter/faker');
    }

    public function run($req, $res, $args)
    {
        $tables = self::getStorage();
        foreach($tables as $table => $count) {
            $class = '\\BBConverter\\Converters\\'.$_SESSION['fakeBoard'].'\\'.ucfirst(Utils::camel($table));
            $count = $class::fake($count);
            if(null === $count) {
                unset($tables[$table]);
            } else {
                $tables[$table] = $count;
                self::saveStorage($tables);
                // redirect to himself
                return View::setPageInfo([
                    'url' => Router::pathFor('Converter.faker.run'),
                    'tables' => $tables,
                    'table' => $table,
                    'count' => $count,
                    'time' => $_SESSION['fakeTime']
                ])->display('@converter/redir');
            }
        }
        Log::info('fill finish by: '.date ('H:i:s', ceil($_SESSION['fakeTime']))
            .', total rows: '.(isset($_SESSION['fakeRows']) ? $_SESSION['fakeRows'] : 0));
        return Router::redirect(Router::pathFor('adminLogs'), ['success', 'Congratulations! All Done!']);
    }

    public function getProviders()
    {
        return [
            'person',
            'address',
            'company',
            'date',
            'phone',
            'integer',
            'text'
        ];
    }
}
