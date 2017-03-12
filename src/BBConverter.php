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

use RunBB\Core\Plugin;
use RunBB\Init;
use RunBB\Middleware\Admin as IsAdmin;
use RunBB\Helpers\Menu\MenuCollection;

class BBConverter extends Plugin
{
    const NAME = 'converter';// config key name
    const TITLE = 'RunBB Converter';
    const DESCRIPTION = 'RunBB Converter';
    const VERSION = '0.1.0';
    const KEYWORDS = [
        'runbb',
        'converter',
        'helper',
        'MyBB',
        'PunBB',
        'FluxBB',
        'CodoForum',
        'bbPress',
        'phpBB'
    ];
    const AUTHOR = [
        'name' => '1f7'
    ];

    /**
     * Back compatibility with featherBB plugins
     *
     * @return string
     */
    public static function getInfo()
    {
        $cfg = [
            'name' => self::NAME,// config key name
            'title' => self::TITLE,
            'description' => self::DESCRIPTION,
            'version' => self::VERSION,
            'keywords' => self::KEYWORDS,
            'author' => self::AUTHOR
        ];
        return json_encode($cfg);
    }

    public static function adminMenu(MenuCollection & $menu)
    {
        Lang::load('converter-index', 'converter', __DIR__ . '/Lang');

        // example one level menu:
        $conv = $menu->createItem('admin-converter', [
            'label' => 'Converter',
            'icon'  => 'paw fa-lg',
            'url'   => Router::pathFor('infoPlugin', ['name' => 'converter'])
        ]);

        // common for any one or more one levels menu
        $menu->addItem('admin-converter', $conv);
    }

    public function run()
    {
        Statical::addNamespace('*', __NAMESPACE__ . '\\*');

        $this->c['hooks']->bind('admin.plugin.menu', [$this, 'getAdminMenu']);

        Route::group(Init::getAdminUrl() . '/converter', function () {
            $this->map(
                ['GET', 'POST'],
                '',
                '\BBConverter\Controller\BBConverter:index'
            )->setName('Converter.home');
            $this->map(
                ['GET', 'POST'],
                '/fake',
                '\BBConverter\Controller\Faker:display'
            )->setName('Converter.faker.display');
            $this->map(
                ['GET', 'POST'],
                '/runfaker',
                '\BBConverter\Controller\Faker:run'
            )->setName('Converter.faker.run');
            $this->map(
                ['GET', 'POST'],
                '/convert',
                '\BBConverter\Controller\BBConverter:display'
            )->setName('Converter.convert.display');
            $this->map(
                ['GET', 'POST'],
                '/runConvert',
                '\BBConverter\Controller\BBConverter:run'
            )->setName('Converter.convert.run');
        })->add(new IsAdmin());
    }

    public function install()
    {
        Statical::addNamespace('*', __NAMESPACE__.'\\*');
    }

    public function remove()
    {
        Statical::addNamespace('*', __NAMESPACE__ . '\\*');
    }
}
