<?php
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
        private static $_routes = array('default' => array('url' => '/^\/(?<controller>[\w\/]+)(\.(?<extension>[a-zA-Z0-9]{2,}))?/i', 'status' => true));

        public static function getInstance(): self {
            return self::$_instance ?? self::$_instance = new self();
        }
        public function ignoreRoutes(array $routes=array()){
            foreach($routes as $k => $v) {
                array_key_exists($k, self::$_routes) && self::$_routes[$k]['status'] = false;
            }
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
