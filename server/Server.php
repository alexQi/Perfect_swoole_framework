<?php

/**
 * class description : swoole server
 * author            : alex
 */

class AppServer{

    public $serv_config;
    public $serv;
    public $fd;
    public $application;
    public static $serve;
    public static $get;
    public static $post;
    public static $header;
    public static $server;

    public function __construct()
    {
        define('Perfect','ZED');
        header("Content-type: text/html; charset=utf-8");
        $system_path = 'framework';

        error_reporting(0);
        ini_set("display_errors", "Off");

        define('ENVIRONMENT', 'debug'); //   debug || product

        require_once __DIR__."/config/server_config.php";
        require_once $system_path.'/core/Perfect.php';

        $this->serv_config = $SERV_CONFIG;
    }

    public function run()
    {
        $this->serv = new swoole_http_server(
            $this->serv_config['host'],
            $this->serv_config['port'],
            $this->serv_config['mode'],
            $this->serv_config['sock_type']
        );
        $this->serv->set(array(
            'reactor_num'    => $this->serv_config['reactor_num'],
            'worker_num'     => $this->serv_config['worker_num'],
            'backlog'        => $this->serv_config['backlog'],
            'max_request'    => $this->serv_config['max_request'],
            'dispatch_mode'  => $this->serv_config['dispatch_mode'],
            'daemonize'      => $this->serv_config['daemonize'],
            'task_worker_num'=> $this->serv_config['task_num'],
        ));

        $this->serv->on('Start'  , array($this, 'onStart'));
        $this->serv->on('Task'   , array($this, 'onTask'));
        $this->serv->on('Request', array($this, 'onRequest'));
        $this->serv->on('Finish' , array($this, 'onFinish'));

        $this->application = new Perfect();
        $this->serv->start();
    }

    public function onStart($serv)
    {
        $db = new Swoole\MYSQL();
        $db->connect([
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '',
            'database' => 'mysql',
        ],function ($db, $result) {
            $db->query("show tables", function (Swoole\MySQL $db, $result) {
                if ($result === false) {
                    var_dump($db->error, $db->errno);
                } elseif ($result === true) {
                    var_dump($db->affected_rows, $db->insert_id);
                } else {
                    var_dump($result);
                    $db->close();
                }
            });
        });
        var_dump($db);
        $this->application->Db = $db;
    }

    public function onRequest(\swoole_http_request $request,\swoole_http_response $response){
        $this->application->server = isset($request->server) ? $request->server:[];
        $this->application->header = isset($request->header) ? $request->header:[];
        $this->application->get    = isset($request->get) ? $request->get:[];
        $this->application->post   = isset($request->post) ? $request->post:[];
        $this->application->serv   = $this->serv;
        ob_start();
        try{
            $this->application->run();
        }catch (Exception $e){
            var_dump($e->getMessage());
        }

        $result = ob_get_contents();
        ob_end_clean();
        $response->end($result);
    }

    public function onTask($serv,$task_id,$src_worker_id, $data)
    {
        $serv->finish($data);
    }

    public function onFinish($serv,$task_id,$data)
    {
        echo "$task_id task finish";
    }
}


$appServer = new AppServer();
$appServer->run();
