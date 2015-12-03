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
        public function exec(){
            $request = Request::getInstance();
            $resource = class_exists($request->_controller) ? new $request->_controller() : new ResourceNotFound();
            //$resource->_data = $request->_data;
            $response = Response::getInstance();

            $resource->exec();

            $response->setStatus($resource->_status);
            $response->setHeader($resource->_headers);
            $response->setBody($resource->_body);
            $response->output();
        }
    }
}
