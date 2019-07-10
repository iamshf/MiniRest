<?php
/**
 * 不存在的资源
 *
 * @author 盛浩锋
 * @date 2019-7-23
 * @version v2.0.0
 * @description 升级为PHP7版本
 */
declare(strict_types=1); 
namespace MiniRest{
    class ResourceNotFound extends Resource {
        public function getHtml() {
            $this->_status = 404;
            $this->_body = '对不起，您请求的资源不存在。';
        }
        public function getJson() {
            $this->_status = 404;
            $this->_body = '{"msg": "对不起，您请求的资源不存在。"}';
        }
        public function getXml() {
            $this->_status = 404;
            $this->_body = '<?xml version="1.0" encoding="UTF-8"?><body>对不起，您请求的资源不存在。</body></xml>';
        }
        public function getText() {
            $this->_status = 404;
            $this->_body = '对不起，您请求的资源不存在。';
        }
    }
}
