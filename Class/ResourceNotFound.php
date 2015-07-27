<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResourceNotFound
 *
 * @author shf
 */
namespace MiniRest{
    class ResourceNotFound extends Resource {
        public function getHtml(){
            $this->_headers[] = 'HTTP/1.1 404 Not Found';
            $this->_body = '找不到您请求的页面';
        }
    }
}
