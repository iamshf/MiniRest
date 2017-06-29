<?php
namespace MiniRest{
    /**
     * Description of Response
     *
     * @author shf
     */
    class Response {
        protected $_body;
        protected $_headers = array();
        public $_status;
        
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

        private static $_instance;
        private function __construct() {
        }
        public static function getInstance() {
            if(!self::$_instance) {
                self::$_instance = new Response();
            }
            return self::$_instance;
        }

		public function __clone(){
			throw new \Exception('Class can not be cloned');
		}

        public function setHeader($headers = array()){
            $this->_headers = array_merge($this->_headers, $headers);
        }
        public function setBody($body){
            $this->_body = $body;
        }
        private function contentEncoding($body, $acceptEncoding){
            if($body && $acceptEncoding && ini_get('zlib.output_compression') == 0){
                
            }
        }

        public function setStatus($statusCode){
            if(array_key_exists($statusCode, $this->_statusMessages)){
                $this->_status = $statusCode;
            }
        }
        public function outputHead(){
            if($this->_status){
                header('HTTP/1.1 '. $this->_status .' ' . $this->_statusMessages[$this->_status]);
                header('Status: ' . $this->_status);
            }
            foreach ($this->_headers as $header){
                header($header);
            }
        }
        public function outputBody(){
            echo $this->_body;
        }

        public function output(){
            $this->outputHead();
            $this->outputBody();
        }
    }
}
