<?php
declare(strict_types=1); 
namespace MiniRest {
    /**
     * 处理请求
     *
     * @author 盛浩锋
     * @date 2019-7-23
     * @version v2.0.0
     * @description 升级为PHP7版本
     */
    class Request {
        private static $_instance;
        protected $_url;
        protected $_method;
        protected $_accepts;
        protected $_contentType;
        protected $_acceptEncoding;
        protected $_ifModifiedSince;
        protected $_ifNoneMatch;
        protected $_data;
        protected $_device;
        protected $_controller;

        public function __get(string $k) {
            return $this->$k;
        }
        public static function getInstance(): self{
            return self::$_instance ?? self::$_instance = new self();
        }
        private function __construct(){
            $this->_url = $_SERVER['REQUEST_URI'];
            $this->_method = strtolower($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? $_SERVER['REQUEST_METHOD']);
            $this->_accepts = $this->getAcceptArray('HTTP_ACCEPT');
            $this->_acceptEncoding = $this->getAcceptArray('HTTP_ACCEPT_ENCODING');
            $this->_contentType = $this->getContentType();
            array_key_exists('HTTP_IF_MODIFIED_SINCE', $_SERVER) && !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $this->_ifModifiedSince = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            array_key_exists('HTTP_IF_NONE_MATCH', $_SERVER) && !empty($_SERVER['HTTP_IF_NONE_MATCH']) && $this->_ifNoneMatch = $_SERVER['HTTP_IF_NONE_MATCH'];
            $this->getData();
            $this->getRoute();
            $this->_device = $this->getDevice();
        }
        private function getAcceptArray(string $k): array {
            $result = array();
            if(array_key_exists($k, $_SERVER) && !empty($_SERVER[$k])) {
                $arr = explode(',', strtolower($_SERVER[$k]));
                $arr_q = array();
                foreach($arr as $v) {
                    $parts = preg_split('/\s*;\s*q=/', $v);
                    is_array($parts) && !empty($parts) && $arr_q[$parts[1] ?? 1][] = trim($parts[0]);
                }
                krsort($arr_q, \SORT_NUMERIC);//按权重因子排序
                array_walk_recursive($arr_q, function($item, $k) use(&$result) {
                    $result[] = $item;
                });
            }
            return $result;
        }
        private function getContentType(): string {
            $contentType = mb_strtolower(trim($_SERVER['HTTP_CONTENT_TYPE'] ?? $_SERVER['CONTENT_TYPE'] ?? 'application/x-www-form-urlencoded'));
            preg_match('/^(?<content_type>[\w\-]+\/[\w\-]+)(;\s?[\w\-=]*)*$/', $contentType, $matches);
            return $matches['content_type'] ?? 'application/x-www-form-urlencoded';
        }
        private function getData(){
            switch ($this->_method) {
            case 'get':
                $this->_data = $_GET;
                break;
            case 'post':
                $this->serializePostData();
                break;
            case 'head':
                $this->_data = $_GET;
                break;
            case 'delete':
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
            case 'application/xml':
            case 'text/xml':
                $this->_data = array_merge($_GET, json_decode(json_encode(simplexml_load_string(file_get_contents('php://input'))), true));
                //后续改为获取xml字符串，由应用程序自己解析
                //$this->_data['request_xml'] = file_get_contents('php://input');
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
        private function getRoute(){
            $routes = (Route::getInstance())->_routes;
            foreach($routes as $route) {
                if($route['status']) {
                    if(preg_match($route['url'], $this->_url, $matches)) {
                        foreach($matches as $k => $v){
                            is_string($k) && $k !== 'controller' && $this->_data[$k] = $v;
                        }
                        break;
                    }
                }
            }
            $this->parseController($matches);
        }
        private function parseController(array $matches) {
            $controllers = explode('/', $matches['controller'] ?? 'index');
            $this->_controller = (defined('\Conf::CONTROLLER_NAMESPACE') ? \Conf::CONTROLLER_NAMESPACE : '') . implode('\\', array_map(function($str) {
                return empty($str) ? 'Index' : ucfirst($str);
            }, $controllers)) . (defined('\Conf::CONTROLLER_SUFFIX') ? \Conf::CONTROLLER_SUFFIX : '');
        }

        private function getDevice(){
            if(array_key_exists('HTTP_USER_AGENT', $_SERVER) && (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|windowswechat/i',$_SERVER['HTTP_USER_AGENT'])||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($_SERVER['HTTP_USER_AGENT'],0,4)))){
                return Device::MOBILEPHONE;
            }
            else {
                return Device::PC;
            }
        }
        private function __clone(){}
        /*
        public $mimeTypes = array(
            'html' => 'text/html',
            'htm' => 'text/html',
            'php' => 'application/php',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml'
        );
         */
    }
}
