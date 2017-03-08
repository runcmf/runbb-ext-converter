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
        if($repair>0) {
            self::repair();

            return Router::redirect(Router::pathFor('adminLogs'), ['success', 'Congratulations! All Done!']);
        }
    }
}
