<?php
namespace MiniRest
{
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * 处理请求
     *
     * @author 盛浩锋
     * @date 2015-7-23
     * @version v1.0.0
     */
    class Request {
        private static $_instance;

        protected $_url;
        protected $_method;
        protected $_data;
        protected $_accepts = array();
        protected $_acceptLanguages = array();
        protected $_contentType = array();
        protected $_acceptEncoding;
        protected $_route = array();
        protected $_controller = 'Index';
        protected $_ifmodifiedsince;


        public $mimeTypes = array(
            'html' => 'text/html',
            'htm' => 'text/html',
            'php' => 'application/php',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml'
        );

        private function __construct() {
            $this->_url = $_SERVER['REQUEST_URI'];
            $requestHeaders = getallheaders();
            $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
            $this->getData();

            $this->_accepts = array_unique(array_merge($this->_accepts, $this->getAcceptArray('Accept', $requestHeaders)));
            $this->_acceptLanguages = array_unique(array_merge($this->_acceptLanguages, $this->getAcceptArray('Accept-Language', $requestHeaders)));
            if(array_key_exists('If-Modified-Since', $requestHeaders) && !empty($requestHeaders['If-Modified-Since'])){
                $this->_ifmodifiedsince = $requestHeaders['If-Modified-Since'];
            }
            $this->getRoute();
        }

        public static function getInstance(){
            if (!self::$_instance)
            {
                self::$_instance = new Request();
            }
            return self::$_instance;
        }

        private function getData(){
            switch ($this->_method){
            case 'GET':
                $this->_data = $_GET;
                break;
            case 'POST':
                $this->_data = $_POST;
                break;
            case 'HEAD':
                $this->_data = $_GET;
                break;
            default :
                parse_str(file_get_contents('php://input'), $this->_data);
                break;
            }
        }

        private function getAcceptArray($acceptName, $requestHeaders){
            $accept = $acceptArray = array();
            if(array_key_exists($acceptName, $requestHeaders) && !empty($requestHeaders[$acceptName])){
                $acceptString = $requestHeaders[$acceptName];
                foreach (explode(',', strtolower($acceptString)) as $part) {
                    $parts = preg_split('/\s*;\s*q=/', $part);
                    if (isset($parts) && isset($parts[1]) && $parts[1]) {
                        $num = $parts[1] * 10;
                    } else {
                        $num = 10;
                    }
                    if ($parts[0]) {
                        $accept[$num][] = $parts[0];
                    }
                }
                krsort($accept);
                foreach ($accept as $parts) {
                    foreach ($parts as $part) {
                        $acceptArray[] = trim($part);
                    }
                }
            }
            return $acceptArray;
        }

        private function getRoute(){
            $routes = new Route();
            foreach ($routes->_routes as $route){
                if($route['status']){
                    preg_match('#' . $route['url'] . '#i', $this->_url, $matches);
                    if(!empty($matches[0])){
                        foreach($matches as $k => $v){
                            if(!is_int($k) && $k !== 'controller'){
                                $this->_data[$k] = $v;
                            }
                        }
                        break;
                    }
                }
            }
            $this->parseController($matches);
        }

        private function parseController($matches){
            $controllers = array($this->_controller);
            if(array_key_exists('controller', $matches) && !empty($matches['controller'])){
                $controllers = explode('/', $matches['controller']);
            }
            $this->_controller = (defined('\Conf::CONTROLLER_NAMESPACE') ? \Conf::CONTROLLER_NAMESPACE : '') . implode('\\', 
                array_map(function($str){
                    return ucfirst($str);
                }, 
                    $controllers)
                ) . (defined('\Conf::CONTROLLER_SUFFIX') ? \Conf::CONTROLLER_SUFFIX : '');
        }

        function __get($name) {
            return isset($this->$name) ? $this->$name : null;
        }
    }
}
