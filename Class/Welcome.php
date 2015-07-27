<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Welcome
 *
 * @author shf
 */
namespace MiniRest
{
    class Welcome extends Resource {
        public function getHtml(){
            $this->_headers[] = 'Content-Type: text/html; charset=utf-8';
            
            $this->_body = $this->render(dirname(__FILE__).'/WelcomeTemplate.html');
        }
        public function getJson(){
            $this->_headers[] = 'Content-Type: text/javascript; charset=utf-8';
            $this->_body = 'alert("Welcome to MiniRest FrameWork")';
        }
        public function getCss(){
            $this->_headers[] = 'Content-Type: text/css;';
            $this->_body = 'body{background-color:black;color:#FFFFFF;}';
        }
        public function getJs(){
            $this->_headers[] = 'Content-Type: text/javascript;';
            $this->_body = 'alert("Welcome to MiniRest FrameWork");';
        }
    }
}