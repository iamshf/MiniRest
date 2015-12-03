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
        public function exec() {
            $this->_response->setStatus(404);
            $this->_body = '您请求的资源不存在';
        }
    }
}
