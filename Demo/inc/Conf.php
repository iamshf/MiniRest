<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Conf
 *
 * @author shf
 */
class Conf{
    const DB_INFO = '{"dsn":"mysql:host=localhost;dbname=BothOfficial;","user":"root","password":"root"}';
    const FILE_PATH = '/home/shf/mydata/documents/work/project/MiniRest/';
    const CONTROLLER_NAMESPACE = '\\Web\\Controller\\';
    const CONTROLLER_SUFFIX = 'Controller';
    const DB = array('id' => 1);

    public function init(){
        $this->setError();
        spl_autoload_register(array($this,"autoload"));
        $this->setResource();
    }
    /**
     * 设置自动包含
     */
    public function autoload($classname){
        $path = self::FILE_PATH.
            strtr($classname,
                array(							
                    'Web\\Controller\\' => 'Demo/Controller/',
                    'MiniRest\\' => 'Class/',
                    "\\"=>"/"
                )
            ).
            '.php';
        file_exists($path) && require_once $path;
        //echo $classname,'<br />',$path,'<br />', file_exists($path), '<br /><br /><br />';
    }
    /**
     * 设置错误显示级别
     */
    private function setError(){
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
    }

    private function setResource(){
        $route = \MiniRest\Route::getInstance();
        $route->addRoutes(array(
            'test' => array(
                'url' => '/^\/(?<controller>admin\/test)\/(?<id>[0-9]+)\/(?<name>[a-zA-Z0-9]+)/i',
                'status' => true
            )
        ));
    }
}

$conf = new Conf();
$conf->init();
