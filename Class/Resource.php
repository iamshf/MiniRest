<?php
namespace MiniRest{
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of Resource
     *
     * @author shf
     */
    abstract class Resource {

        protected $_status;
        protected $_headers = array();
        protected $_body;
        protected $_response;
        protected $_request;

        public function __construct() {
            $this->_response = Response::getInstance();
            $this->_request = Request::getInstance();
        }
        protected function unSupportedMedia() {
            $this->_response->setStatus(415);
            $this->_body = '您请求的资源不支持';
        }

        public function exec(){
            $accept = $this->checkAccept();
            $methodName = strtolower($this->_request->_method) . $accept;

            method_exists($this, $methodName) ? $this->$methodName() : $this->unSupportedMedia();
        }
        protected function checkAccept(){
            $accepts = $this->_request->_accepts;
            $accept = 'Html';
            if($accepts[0]){
                switch (strtolower($accepts[0])){
                case 'application/json':
                    $accept = 'Json';
                    break;
                case 'text/javascript':
                    $accept = 'Js';
                    break;
                case 'application/javascript':
                    $accept = 'Js';
                    break;
                case 'text/css':
                    $accept = 'Css';
                    break;
                case 'text/plain':
                    $accept = 'Text';
                    break;
                default :
                    $accept = 'Html';
                    break;
                }
            }
            return $accept;
        }

        protected function isModified($file){
            if(strtotime($this->_request->_ifmodifiedsince) >= filemtime($file)){
                $this->_response->setStatus(304);
                return false;
            }
            return true;
        }

        private function validateUrl(){

        }

        protected function render($template, $view = array()){
            extract($view);
            ob_end_clean();
            ob_start();
            require_once $template;
            $content = ob_get_contents();
            ob_end_clean();
            ob_start();

            return $content;
        }

        public function __get($name) {
            return isset($this->$name) ? $this->$name : null;
        }
        public function __set($name, $value) {
            //if(isset($this->$name)){
            $this->$name = $value;
            //}
        }
    }
}
