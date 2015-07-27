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
	const FILE_PATH = '/mydata/Web/Test/';
    const CONTROLLER_NAMESPACE = '\\Web\\Controller\\';

    public function init(){
        $this->setError();
        spl_autoload_register(array($this,"autoload"));
        $this->setResource();
    }
    /**
     * 设置自动包含
     */
    public function autoload($classname){

        $path = Conf::FILE_PATH.
			strtr($classname,
					array(							
                        'BOSI\\Common\\'=>'CommLib/',
                        'BOSI\\Official\\'=>'/',
                        'MiniRest\\' => 'CommLib/Rest/',
                        "\\"=>"/"
					)
			).
			'.php';
        
    //echo $classname,'<br />',$path,'<br />';
        if(file_exists($path)){
            require_once $path;
        }
 else {
            echo $path;
 }
    }
    /**
     * 设置错误显示级别
     */
    private function setError(){
        ini_set('display_errors', 'On');
        error_reporting(E_ALL);
    }
    
    private function setResource(){
        $route = new MiniRest\Route();
        $route->addRoutes(array(
            'test' => array(
                'url' => '/(?<controller>admin/test)/(?<id>[0-9]+)/(?<name>[a-zA-Z0-9]+)',
                'status' => true
            )
        ));
    }
}

$conf = new Conf();
$conf->init();