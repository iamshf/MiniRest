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
        
        protected $_headers = array();
        protected $_body;
        
        private $_request;
        
        protected $_data;

        public function exec(){
            $accept = $this->checkAccept();
            $className = strtolower($this->_request->_method) . $accept;

            $this->$className();
        }
        private function checkAccept(){
            $accepts = $this->_request->_accepts;
            $accept = 'Html';
            
            if($accepts[0]){
                switch ($accepts[0]){
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
                    default :
                        $accept = 'Html';
                        break;
                }
            }
            return $accept;
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