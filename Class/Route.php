<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 处理请求
 *
 * @author 盛浩锋
 * @date 2019-7-23
 * @version v2.0.0
 * @description 升级为PHP7版本
 */
declare(strict_types=1); 
namespace MiniRest {
    class Route {
        private static $_instance;
        private static $_routes = array('default' => array('url' => '/^\/(?<controller>[\w\/]+)/i', 'status' => true));

        public static function getInstance(): self {
            return self::$_instance ?? self::$_instance = new self();
        }
        public function igonRoutes(array $routes=array()){
            array_key_exists($k, $this->routes) && self::$_routes[$k]['status'] = false;
        }
        public function addRoutes(array $routes = array()) {
            foreach(self::$_routes as $k => $v) {
                !array_key_exists($k, $routes) && $routes[$k] = $v;
            }
            self::$_routes = $routes;
        }
        function __get(string $k) {
            return $this->$k ?? self::$_routes;
        }
        private function __construct(){}
        private function __clone(){}
    }
}
