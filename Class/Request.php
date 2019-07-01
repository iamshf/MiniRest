<?php
namespace MiniRest
{
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * 处理请求
     *
     * @author 盛浩锋
     * @date 2015-7-23
     * @version v1.0.0
     */
    class Request {
        private static $_instance;

        protected $_url;
        protected $_method;
        protected $_data;
        protected $_accepts = array();
        protected $_acceptLanguages = array();
        protected $_contentType = array();
        protected $_acceptEncoding;
        protected $_route = array();
        protected $_controller = 'Index';
        protected $_ifmodifiedsince;
        protected $_device;

        public $mimeTypes = array(
            'html' => 'text/html',
            'htm' => 'text/html',
            'php' => 'application/php',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml'
        );

        private function __construct() {
            $this->_url = $_SERVER['REQUEST_URI'];
            $this->_method = strtoupper(array_key_exists('HTTP_X_HTTP_METHOD_OVERRIDE', $_SERVER) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD']);
            $this->_contentType = $this->getContentType();
            $this->_accepts = array_unique(array_merge($this->_accepts, $this->getAcceptArray('HTTP_ACCEPT')));
            $this->_acceptLanguages = array_unique(array_merge($this->_acceptLanguages, $this->getAcceptArray('HTTP_ACCEPT_LANGUAGE')));
            if(array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER) && !empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $this->_ifmodifiedsince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            }
            $this->_device = $this->getDevice();
            $this->getData();
            $this->getRoute();
        }

        public static function getInstance(){
            if (!self::$_instance)
            {
                self::$_instance = new Request();
            }
            return self::$_instance;
        }

        private function getData(){
            switch ($this->_method) {
                case 'GET':
                    $this->_data = $_GET;
                    break;
                case 'POST':
                    $this->serializePostData();
                    break;
                case 'HEAD':
                    $this->_data = $_GET;
                    break;
                case 'DELETE':
                    parse_str(file_get_contents('php://input'), $data);
                    $this->_data = array_merge($_GET, $data);
                    break;
                default :
                    parse_str(file_get_contents('php://input'), $this->_data);
                    break;
            }
        }
        private function serializePostData() {
            switch($this->_contentType) {
                case 'application/x-www-form-urlencoded':
                    $this->_data = array_merge($_GET, $_POST);
                    break;
                case 'multipart/form-data':
                    $this->_data = array_merge($_GET, $_POST);
                    !empty($_FILES) && $this->_data['files'] = $_FILES;
                    break;
                case 'text/xml':
                    $this->_data = array_merge($_GET, json_decode(json_encode(simplexml_load_string(file_get_contents('php://input'))), true));
                    break;
                case 'application/xml':
                    $this->_data = array_merge($_GET, json_decode(json_encode(simplexml_load_string(file_get_contents('php://input'))), true));
                    break;
                case 'application/json':
                    $this->_data = array_merge($_GET, json_decode(file_get_contents('php://input'), true));
                    break;
                default:
                    parse_str(file_get_contents('php://input'), $data);
                    $this->_data = array_merge($_GET, $data);
                    break;
            }
        }
        private function getDevice(){
            if(array_key_exists('HTTP_USER_AGENT', $_SERVER) && (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|windowswechat/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4)))){
                return Device::MOBILEPHONE;
            }
            else {
                return Device::PC;
            }
        }

        private function getAcceptArray($acceptName){
            $accept = $acceptArray = array();
            if(array_key_exists($acceptName, $_SERVER) && !empty($_SERVER[$acceptName])){
                $acceptString = $_SERVER[$acceptName];
                foreach (explode(',', strtolower($acceptString)) as $part) {
                    $parts = preg_split('/\s*;\s*q=/', $part);
                    if (isset($parts) && isset($parts[1]) && $parts[1]) {
                        $num = $parts[1] * 10;
                    } else {
                        $num = 10;
                    }
                    if ($parts[0]) {
                        $accept[$num][] = $parts[0];
                    }
                }
                krsort($accept);
                foreach ($accept as $parts) {
                    foreach ($parts as $part) {
                        $acceptArray[] = trim($part);
                    }
                }
            }
            return $acceptArray;
        }
        private function getContentType() {
            $contentType = mb_strtolower(array_key_exists('HTTP_CONTENT_TYPE', $_SERVER) ? $_SERVER['HTTP_CONTENT_TYPE'] : (array_key_exists('CONTENT_TYPE', $_SERVER) ? $_SERVER['CONTENT_TYPE'] : 'application/x-www-form-urlencoded'), 'UTF-8');
            preg_match('/^(?<content_type>[\w\-]+\/[\w\-]+)(;\s?[\w\-=]*)*$/', $contentType, $matches);
            return array_key_exists('content_type', $matches) && !empty($matches['content_type']) ? $matches['content_type'] : 'application/x-www-form-urlencoded';
        }

        private function getRoute(){
            $routes = new Route();
            foreach ($routes->_routes as $route){
                if($route['status']){
                    preg_match('#' . $route['url'] . '#i', $this->_url, $matches);
                    if(!empty($matches[0])){
                        foreach($matches as $k => $v){
                            if(!is_int($k) && $k !== 'controller'){
                                $this->_data[$k] = $v;
                            }
                        }
                        break;
                    }
                }
            }
            $this->parseController($matches);
        }

        private function parseController($matches){
            $controllers = array($this->_controller);
            if(array_key_exists('controller', $matches) && !empty($matches['controller'])){
                $controllers = explode('/', $matches['controller']);
            }
            $this->_controller = (defined('\Conf::CONTROLLER_NAMESPACE') ? \Conf::CONTROLLER_NAMESPACE : '') . implode('\\', 
                array_map(function($str){
                    return ucfirst($str);
                }, 
                    $controllers)
                ) . (defined('\Conf::CONTROLLER_SUFFIX') ? \Conf::CONTROLLER_SUFFIX : '');
        }

        function __get($name) {
            return isset($this->$name) ? $this->$name : null;
        }
    }
}
