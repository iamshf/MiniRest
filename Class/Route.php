<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Route
 *
 * @author shf
 */
namespace MiniRest
{
    class Route {
        private static $_routes = array(
            'default'=>array(
                'url' => '',
                'controler' => '',
                'status' => true
            )
        );
        public function igonRoutes($routes=array()){
            foreach ($routes as $k => $v){
                if(array_key_exists($k, $this->routes)){
                    self::$_routes[$k]['status'] = false;
                }
            }
        }
        public function addRoutes($routes = array()){
            foreach ($routes as $k => $v){
                self::$_routes[$k] = $v;
            }
        }
                
        function __get($name) {
            if($name = 'routes'){
                return self::$_routes;
            }
            else{
                return $this->$name;
            }
        }
    }
}