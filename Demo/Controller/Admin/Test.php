<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test
 *
 * @author shf
 */
namespace Web\Controller\Admin{
    class Test extends \MiniRest\Resource {
        public function getHtml(){
            $this->_headers[] = 'Content-Type: text/html; charset=utf-8';
            $body = '';
            foreach ($this->_data as $k => $v){
                $body .= $k . '=' . $v . '<br />';
            }
            $this->_body = $body;
        }
    }
}