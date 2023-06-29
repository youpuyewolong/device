<?php

namespace Wxlmaidou\Device;

trait Tcp
{
    public $server;
    public $fd;
    public $port = 8085;
    public function do()


    {

        $server = new \Swoole\Server('0.0.0.0', $this->port);
        $this->server = $server;
        $server->on('Connect', function ($server, $fd) {
            echo "Client: Connect.\n";
        });

        $server->on('Receive', function ($server, $fd, $reactor_id, $data) {

            $start_position = strpos($data, "<");
            $end_position = strrpos($data, ">");
            $real_str = substr($data,$start_position,($end_position - $start_position + 1));

            $xmlObject = simplexml_load_string($real_str);
            $rootNode = $xmlObject->getName();
            $json = json_encode($xmlObject);
            $res = json_decode($json,true);
            $this->$rootNode($res,$fd);

        });

        $server->on('Close', function ($server, $fd) {
            echo "Client: Close.\n";
        });

        $server->start();


    }

    public function __call($method, $parameters) {
        echo '进入了魔术方法'.PHP_EOL;
        var_dump($method);
        var_dump($parameters);
    }

    public function TIME_SYSNC_REQ($res,$fd){

        $time = date("YmdHis");
        $sendxmlData =
            '<TIME_SYSNC_RES>
            <uuid>'.$res['uuid'].'</uuid>
            <ret>0</ret>
            <time>'.$time.'</time>
            <uploadInterval>0001</uploadInterval>
            <dataStartTime>0001</dataStartTime>
            <dataEndTime>2359</dataEndTime>
            </TIME_SYSNC_RES>';
        $this->server->send($fd, $sendxmlData);
    }
}
