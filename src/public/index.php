<?php

error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\Application as BaseApplication;
use MongoDB\Driver\Manager;
use MongoDB\Client;

class Application extends BaseApplication
{
    /**
     * Register the services here to make them general or register in the ModuleDefinition to make them module-specific
     */
    protected function registerServices()
    {
        define("APP_PATH",dirname(__DIR__).'/app');
        define("ADMIN_PATH",APP_PATH."/admin");
        define("FRONT_PATH",APP_PATH."/frontend");
        
        require_once dirname(APP_PATH)."/vendor/autoload.php";

        $di = new FactoryDefault();

        $loader = new Loader();

        /**
         * We're a registering a set of directories taken from the configuration file
         */
        $loader
            ->registerDirs([APP_PATH.'/library/'])
            ->registerDirs([ADMIN_PATH.'/controllers/'])
            ->registerDirs([FRONT_PATH.'/controllers/'])
            ->register();

        // Registering a router
        $di->set('router', function () {

            $router = new Router();

            $router->setDefaultModule("frontend");

            $router->add('/view', [
                'module'     => 'frontend',
                'controller' => "index",
                'action'     => "view",
            ])->setName('frontend');

            $router->add("/admin/:controller/:action", [
                'module'     => 'admin',
                'controller' => 1,
                'action'     => 2,
            ])->setName('adminFull');

            $router->add("/admin/:controller", [
                'module'     => 'admin',
                'controller' => 1,
                'action'     =>'index',
            ])->setName('adminController');
            $router->add("/admin", [
                'module'     => 'admin',
                'controller' => 'index',
                'action'     => 'index',
            ])->setName('adminBase');

            return $router;
        });
        // Registering escaper
        $di->setShared("escaper", function () {
            return new Phalcon\Escaper();
        });

        $di->setShared(
            "mongo",
            function(){
                $mongo= new Client(
                    'mongodb://root:secret@mongo'
                );
                return $mongo->store;
            }
        );
        $this->setDI($di);
    }

    public function main()
    {

        $this->registerServices();

        // Register the installed modules
        $this->registerModules([
            'frontend' => [
                'className' => 'Frontend\Module',
                'path'      => FRONT_PATH.'/Module.php'
            ],
            'admin'  => [
                'className' => 'Admin\Module',
                'path'      => ADMIN_PATH.'/Module.php'
            ]
        ]);
        $response = $this->handle($_SERVER["REQUEST_URI"]);
        $response->send();
    }
}
try {
    $application = new Application();
    $application->main();
} catch (Exception $e) {
    echo "<pre>";
    echo $e->getMessage() . PHP_EOL;
    echo $e->getFile() . " : " . $e->getLine() . PHP_EOL;
    die("</pre>");
}
