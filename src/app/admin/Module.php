<?php

namespace Admin;

use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\DiInterface;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Db\Adapter\Pdo\Mysql as Database;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(
            [
                'Admin\Controllers' => ADMIN_PATH.'/controllers/',
                'Admin\Models'      => ADMIN_PATH.'/models/',
                'Admin\Plugins'     => ADMIN_PATH.'/plugins/',
            ]
        );

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {
        // Registering a dispatcher
        $di->set('dispatcher', function () {
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Admin\Controllers\\');
            return $dispatcher;
        });

        // Registering the view component
        $di->set('view', function () {
            $view = new View();
            $view->setViewsDir(ADMIN_PATH.'/views/');
            return $view;
        });

        // Set a different connection in each module
        // $di->set('db', function () {
        //     return new Database(
        //         [
        //             "host" => "localhost",
        //             "username" => "root",
        //             "password" => "secret",
        //             "dbname" => "invo"
        //         ]
        //     );
        // });
    }
}