<?php

namespace MiniRest
{
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of Autoloader
     *
     * @author shf
     */
    class Autoloader {
        //put your code here
        public function __construct(){
            spl_autoload_register(array($this,"loadRestfulClass"));
        }
        public function loadRestfulClass($className){
            $dirName = dirname(__FILE__);
            echo $dirName . str_replace('MiniRest\\', '/', $className) . '.php','<br />';
            
            require_once $dirName . str_replace('MiniRest\\', '/', $className) . '.php';
        }
    }
    new Autoloader();
}