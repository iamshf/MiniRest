<?php
declare(strict_types=1); 
namespace MiniRest{
    /**
     * 处理资源
     *
     * @author 盛浩锋
     * @date 2019-7-23
     * @version v2.0.0
     * @description 升级为PHP7版本
     */
    abstract class Resource {
        protected $_status = 200;
        protected $_headers = array();
        protected $_body = '';
        protected $_request;
        protected $_setETag = true;

        private $_lastModifiedTime;
        public function __construct() {
            $this->_request = Request::getInstance();
        }
        public function exec() {
            $methodName = $this->getMethod();
            method_exists($this, $methodName) ? $this->$methodName() : $this->unSupportedMedia();
            $this->isModified();
            $this->setEtag();
        }
        protected function unSupportedMedia() {
            $this->_status = 415;
            $this->_body = '您请求的资源不支持';
        }
        protected function getMethod(): ?string {
            foreach($this->_request->_accepts as $accept) {
                switch($accept) {
                    case 'text/html':
                        $value = 'Html';
                        break;
                    case 'application/json':
                        $value = 'Json';
                        break;
                    case 'application/javascript':
                    case 'text/javascript':
                        $value = 'Js';
                        break;
                    case 'text/css':
                        $value = 'Css';
                        break;
                    case 'text/plain':
                        $value = 'Text';
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $value = 'Xml';
                        break;
                }
                if(method_exists($this, $this->_request->_method . $value)) {
                    $this->_headers[] = 'Content-Type:' . $accept . '; charset=utf-8';
                    return $this->_request->_method . $value;
                }
            }
            //默认为html
            $this->_headers[] = 'Content-Type:text/html; charset=utf-8';
            return $this->_request->_method . $value;
        }

        protected function setEtag() {
            $this->_setETag && $this->_headers[] = 'ETag:' . '"'. hash('md5', $this->_body) .'"';
        }
        protected function setLastModifiedSince(int $timestamp) {
            $this->_lastModifiedTime = $timestamp;
            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", $timestamp) . ' GMT';
        }
        protected function setCacheControl(string $type = 'private', int $time = 0) {
            $this->_headers[] = 'Cache-Control: max-age=' . $expire;
        }
        protected function isModified() {
            $etag = '"' . hash('md5', $this->_body) . '"';
            $req_lastModifiedSince = is_string($this->_request->_ifModifiedSince) ? strtotime($this->_request->_ifModifiedSince) : null;
            if($this->_request->_ifNoneMatch == $etag || $this->_request->_ifNoneMatch == 'W/' . $etag || (is_int($req_lastModifiedSince) && is_int($this->_lastModifiedTime) && $req_lastModifiedSince > $this->_lastModifiedTime)) {
                $this->_status = 304;
                $this->_body = '';
            }
        }
        protected function render(string $template, array $view = array()) {
            extract($view);
            ob_end_clean();
            ob_start();
            require_once $template;
            $content = ob_get_contents();
            ob_end_clean();
            ob_start();
            return $content;
        }
        public function __get(string $k) {
            return $this->$k ?? null;
        }
        public function __set($k, $v) {
            $this->$k = $v;
        }
    }
}
