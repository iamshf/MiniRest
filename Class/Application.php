<?php
/**
 * 框架入口
 *
 * @author 盛浩锋
 * @date 2019-7-23
 * @version v2.0.0
 * @description 升级为PHP7版本
 */
declare(strict_types=1); 
namespace MiniRest{
    use MiniRest\{Request,Resource,Response};
    class Application {
        public function exec() {
            $this->autoload();
            $request = Request::getInstance();
            $resource = class_exists($request->_controller) ? new $request->_controller() : new ResourceNotFound();

            $response = Response::getInstance();
            $response->setStatus($resource->_status);
            $response->setHeader($resource->_headers);
            $response->setBody((string)$resource->_body);
            $response->output();
        }
        private function autoload() {
            spl_autoload_register(function($classname) {
                $file = dirname(__FILE__) . str_replace('MiniRest\\', '/', $classname) . '.php';
                file_exists($file) && require_once $file;
            });
        }
    }
}
