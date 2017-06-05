<?php

/**
 * class description : swoole server
 * author            : alex
 */

class Server{

    public $serv_config;
    public $serv;
    public $fd;
    public $application;

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
        $this->serv = new swoole_server(
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

        $this->serv->on('Worker' , array($this, 'onWorker'));
        $this->serv->on('Task'   , array($this, 'onTask'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Finish' , array($this, 'onFinish'));

        $this->application = new Perfect();
        $this->serv->start();
    }

    public function onWorker($serv,$work_id)
    {
        echo "work start....\n";
    }

    public function onReceive($serv,$fd,$reactor_id,$data)
    {
        # 处理数据
        $argv = explode("\r\n",$data);
        $uri  = explode(' ',$argv[0]);
        $method       = $uri[0];
        $paramsString = explode('?',$uri[1]);
        if (isset($paramsString[1])){
            $paramUri     = $paramsString[1];

            $paramArray   = array();
            foreach(explode('&',$paramUri) as $param)
            {
                $param = explode('=',$param);
                $paramArray[$param[0]] = $param[1];
            }
        }
        ob_start();
        try{
            echo "<pre>";
            var_dump($_SERVER);
//            $_REQUEST = $paramArray;
//            $this->application->run();
        }catch (Exception $e){
            var_dump($e->getMessage());
        }

        $result = ob_get_contents();
        ob_end_clean();

        $this->response($fd,$result);
        $this->serv->close($fd);
    }

    public function onTask($serv,$task_id,$src_worker_id, $data)
    {
        $serv->finish($data);
    }

    public function onFinish($serv,$task_id,$data)
    {
        echo "$task_id task finish";
    }

    /**
     * 发送内容
     * @param resource $serv
     * @param int $fd
     * @param string $respData
     * @return void
     */
    public function response($fd,$respData)
    {
        //响应行
        $response = array(
            'HTTP/1.1 200',
        );
        //响应头
        $headers = array(
            'Server'=>'SwooleServer',
            'Content-Type'=>'text/html;charset=utf8',
            'Content-Length'=>strlen($respData),
        );
        foreach($headers as $key=>$val){
            $response[] = $key.':'.$val;
        }
        //空行
        $response[] = '';
        //响应体
        $response[] = $respData;
        $send_data = join("\r\n",$response);
        $this->serv->send($fd, $send_data);
    }
}


$server = new Server();
$server->run();
