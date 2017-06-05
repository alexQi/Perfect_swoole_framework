<?php
/**
 * swoole sever config
 */

$SERV_CONFIG = array(
    'host'          => '127.0.0.1',
    'port'          => 8088,
    'mode'          => SWOOLE_PROCESS,
    'sock_type'     => SWOOLE_SOCK_TCP,
    'reactor_num'   => 2,
    'worker_num'    => 2,
    'task_num'      => 8,
    'backlog'       => 128,
    'max_request'   => 2000,
    'dispatch_mode' => 1,
    'daemonize'     => 0,

    #心跳检测   websocket tcp 适用
//    'heartbeat_check_interval' => 30,
//    'heartbeat_idle_time'      => 60,
);

