<?php
/**
 * 处理输出相关
 *
 * @author 盛浩锋
 * @date 2019-7-23
 * @version v2.0.0
 * @description 升级为PHP7版本
 */
declare(strict_types=1); 
namespace MiniRest {
    class Response {
        protected $_body;
        protected $_headers = array();
        private $_status;
        private static $_instance;
        protected $_statusMessages = array(
            100 => 'Continue',
            101 => 'Switching Protocols',

            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',

            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',

            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            421 => 'There are too many connections from your internet address',
            422 => 'Unprocessable Entity',
            423 => 'Locked',
            424 => 'Failed Dependency',
            425 => '',
            426 => '',

            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'            
        );

        public static function getInstance(): self {
            return self::$_instance ?? self::$_instance = new self();
        }
        public function setHeader(array $headers = array()){
            $this->_headers = array_merge($this->_headers, $headers);
        }
        public function setBody(string $body) {
            $this->_body = $body;
        }
        public function setStatus(int $statusCode = 200) {
            array_key_exists($statusCode, $this->_statusMessages) && $this->_status = $statusCode;
        }
        public function outputHead(){
            is_int($this->_status) && http_response_code($this->_status);
            // $this->_headers[] = 'ETag: "' . hash('md5', $this->_body) . '"';
            foreach ($this->_headers as $k => $v) {
                if(is_numeric($k) && preg_match('/.+:.+/', $v)) {
                    [$k, $v] = explode(':', $v);
                }
                if(trim((string)$k) != 'Status Code') {
                    header((trim((string)$k) . ': ' . trim((string)$v)));
                }
            }
        }
        public function outputBody() {
            if(isset($this->_body) && !is_null($this->_body) && $this->_status != 304) {
                echo $this->_body;
            }
        }
        public function output() {
            $this->outputHead();
            $this->outputBody();
        }
        private function __construct() {}
        private function __clone() {}
    }
}
