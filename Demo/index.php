<?php
    use MiniRest\Application;
    require_once 'inc/Conf.php';
    $app = new Application();
    $app->exec();
    
//    $a = new admin\test();

//
//if(!$_SERVER['PHP_AUTH_USER']){
//header('WWW-Authenticate: Basic realm="My Realm"');
//header('HTTP/1.1 401 Unauthorized');
//}
// else {
//    echo $_SERVER['PHP_AUTH_USER'],'<br />',$_SERVER['PHP_AUTH_PW'],'<br /><a href="http://www.baidu.com">百度</a>';
//}

//header($string)
//echo 'abc';