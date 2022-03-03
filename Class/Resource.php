<?php
/**
 * 处理资源
 *
 * @author 盛浩锋
 * @date 2019-7-23
 * @version v2.0.0
 * @description 升级为PHP7版本
 */
declare(strict_types=1); 
namespace MiniRest{
    use MiniRest\Request;
    abstract class Resource {
        protected $_status = 200;
        protected $_headers = array();
        protected $_body = '';
        protected $_request;
        protected $_setETag = true;

        private $_lastModifiedTime;
        public function __construct() {
            $this->_request = Request::getInstance();
            $this->exec();
        }
        public function exec(?string $methodName = null) {
            $methodName = $methodName ?? $this->getMethod();
            method_exists($this, $methodName) ? $this->$methodName() : $this->unSupportedMedia();
            $this->isModified();
            $this->setEtag();
        }
        protected function unSupportedMedia() {
            $this->_status = 415;
            if(!empty($extension = strtolower(trim($this->_request->_data['extension'] ?? '')))) {
                switch($extension) {
                    case 'html':
                    case 'htm':
                        $this->_body = '<script type="text/javascript">alert("您请求的资源不支持");</script>';
                        break;
                    case 'json':
                        $this->_body = '{"msg": "您请求的资源不支持"}';
                        break;
                    case 'xml':
                        $this->_body = '<?xml version="1.0" encoding="UTF-8"?><body><msg>您请求的资源不支持</msg></body>';
                        break;
                }
                if(!empty($this->_body)) { return; }
            }
            foreach($this->_request->_accepts as $accept) {
                switch($accept) {
                    case 'application/json':
                        $this->_body = '{"msg": "您请求的资源不支持"}';
                        break;
                    case 'application/javascript':
                    case 'text/javascript':
                        $this->_body = '<script type="text/javascript">alert("您请求的资源不支持");</script>';
                        break;
                    case 'application/xml':
                    case 'text/xml':
                        $this->_body = '<?xml version="1.0" encoding="UTF-8"?><body><msg>您请求的资源不支持</msg></body>';
                        break;
                }
            }
            empty($this->_body) && $this->_body = '您请求的资源不支持';
        }
        protected function getMethod(): ?string {
            if(!empty($extension = strtolower(trim($this->_request->_data['extension'] ?? '')))) {
                switch($extension) {
                    case 'html':
                    case 'htm':
                        $value = 'Html';
                        $this->_headers[] = 'Content-Type: text/html; charset=utf-8';
                        break;
                    case 'json':
                        $value = 'Json';
                        $this->_headers[] = 'Content-Type: application/json; charset=utf-8';
                        break;
                    case 'xml':
                        $value = 'Xml';
                        $this->_headers[] = 'Content-Type: application/xml; charset=utf-8';
                        break;
                    case 'txt':
                    case 'lrc':
                        $value = 'Text';
                        $this->_headers[] = 'Content-Type: text/plain; charset=utf-8';
                        break;
                    case 'js':
                        $value = 'Js';
                        $this->_headers[] = 'Content-Type: application/javascript; charset=utf-8';
                        break;
                    case 'css':
                        $value = 'Css';
                        $this->_headers[] = 'Content-Type: text/css; charset=utf-8';
                        break;
                    case 'jpg':
                    case 'jpeg':
                        $value = 'Image';
                        $this->_headers[] = 'Content-Type: image/jpeg';
                        break;
                    case 'png':
                    case 'gif':
                        $value = 'Image';
                        $this->_headers[] = 'Content-Type: image/' . $extension;
                        break;
                    case 'mp3':
                        $value = 'Audio';
                        $this->_headers[] = 'Content-Type: audio/' . $extension;
                        break;
                    case 'mp4':
                        $value = 'Video';
                        $this->_headers[] = 'Content-Type: audio/' . $extension;
                        break;
                    case 'zip':
                        $value = 'Zip';
                        $this->_headers[] = 'Content-Type: application/x-zip-compressed';
                        break;
                }
                if(!empty($value)) {
                    return $this->_request->_method . $value;
                }
            }
            foreach($this->_request->_accepts as $accept) {
                $value = '';
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
                    case 'audio/mp3':
                        $value = 'Audio';
                        break;
                    case 'video/mp4':
                        $value = 'Video';
                        break;
                    case 'application/x-zip-compressed':
                        $value = 'Zip';
                        break;
                }
                if(!empty($value)) {
                    $this->_headers[] = 'Content-Type:' . $accept . '; charset=utf-8';
                    return $this->_request->_method . $value;
                }
            }
            //默认为html
            $this->_headers[] = 'Content-Type:text/html; charset=utf-8';
            return $this->_request->_method . 'html';
        }

        protected function setEtag() {
            $this->_setETag && $this->_headers[] = 'ETag:' . '"'. hash('md5', (string)$this->_body) .'"';
        }
        protected function setLastModifiedSince(int $timestamp) {
            $this->_lastModifiedTime = $timestamp;
            $this->_headers[] = 'Last-Modified: ' . gmdate("D, d M Y H:i:s", $timestamp) . ' GMT';
        }
        protected function setCacheControl(string $value = 'private') {
            $this->_headers[] = 'Cache-Control: ' . $value;
        }
        protected function isModified() {
            $etag = '"' . hash('md5', (string)$this->_body) . '"';
            $req_lastModifiedSince = is_string($this->_request->_ifModifiedSince) ? strtotime($this->_request->_ifModifiedSince) : null;
            if($this->_request->_ifNoneMatch == $etag || $this->_request->_ifNoneMatch == 'W/' . $etag || (is_int($req_lastModifiedSince) && is_int($this->_lastModifiedTime) && $req_lastModifiedSince > $this->_lastModifiedTime)) {
                $this->_status = 304;
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
