<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Application
 *
 * @author shf
 */

namespace MiniRest{
    class Application {
        private $_request;
        private $_resource;
        private $_response;
        
        public function exec(){
            $this->_request = Request::getInstance();
            
            $this->getResource();
            
            $this->_response = new Response();
            
            $this->_resource->_request = $this->_request;
            $this->_resource->_response = $this->_response;
            $this->_resource->_data = $this->_request->_data;
            $this->_resource->exec();
            
            $this->_response->setHeader($this->_resource->_headers);
            $this->_response->setBody($this->_resource->_body);
            $this->_response->output();
        }
        
        private function getResource(){
            if($this->_request->_controller) {
                $controller = \Conf::CONTROLLER_NAMESPACE . $this->_request->_controller;
                $this->_resource = new $controller();
            }
            if(!$this->_resource){
                $this->_resource = new ResourceNotFound();
            }
        }
    }
}