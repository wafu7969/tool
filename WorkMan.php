<?php

/**
 * Created by Qiang.
 * User: Fuqiang Wang
 * Date: 2018/12/2
 * Time: 13:08
 * 微信二维码支付
 * 神灯智能商家数据看板实例
 * Thinkphp 5.0
 */



/**
 * 增加 server.php文件内容如下

define('APP_PATH', __DIR__ . '/application/');
define('BIND_MODULE','admin/Worker');
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';


 *
 *启动：php server.php -d    （注意php安装的路径或者是否设置path）
 *
 */



/**
 * 根据上面BIND_MODULE的参数  新建worker控制器
 *内容如下
 *
 */

namespace app\admin\controller;

use think\worker\Server;
use app\admin\event\DataBoardEvent;
use think\cache;
class Worker extends Server
{
    protected $socket = 'websocket://192.168.1.200:2346';

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        // 客户端传递的是json数据
        $message_data = json_decode($data, true);

        if(!$message_data)
        {
            return ;
        }
        if(!isset($message_data['join_code']) || $message_data['join_code'] == '')
        {
            $connection->send(json_encode(['code'=>0,'msg'=>'商家编码必须','data'=>'']));
        }
        else if(!isset($message_data['token']) || $message_data['token'] == '')
        {
            $connection->send(json_encode(['code'=>0,'msg'=>'token必须','data'=>'']));
        }
        else if(!cache("admin_".$message_data['token']))
        {
            $connection->send(json_encode(['code'=>-1,'msg'=>'token过期','data'=>'']));
        }
        else
        {
            // 根据类型执行不同的业务
            switch($message_data['type'])
            {
                // 客户端回应服务端的心跳
                case 'pong':

                    return ;

                // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
                case 'board':
                    $dataBoardEvent=new DataBoardEvent();
                    $info = $dataBoardEvent->getMess($message_data);
                    $list = [];
                    foreach ($info as $k => $v)
                    {
                        $name = '';
                        switch ($k)
                        {
                            case 'ach':
                                $name = '业绩';
                                break;
                            case 'exp':
                                $name = '消耗';
                                break;
                            case 'pro':
                                $name = '项目数';
                                break;
                            case 'num':
                                $name = '客次';
                                break;
                            case 'appo':
                                $name = '预约';
                                break;
                            case 'valid':
                                $name = '有效客';
                                break;
                            case 'drain':
                                $name = '引流';
                                break;
                            case 'admit':
                                $name = '人均接待';
                                break;
                            case 'handl':
                                $name = '人均操作';
                                break;
                            case 'equal':
                                $name = '客均项目';
                                break;
                            case 'price':
                                $name = '客均单价';
                                break;
                        }
                        $v['name'] = $name;
                        $list[] = $v;
                    }
                    $connection->send(json_encode(['code'=>1,'msg'=>'ok','data'=>$list]));

                case 'say':

                    return;
            }
        }
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {

    }
    /**
     * 向所有验证的用户发送消息
     */
    public function sendAllMessage(){
        global $worker;
        foreach($worker->uidConnections as $connection)
        {
            $connection->send($message);
        }
    }
    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {

    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }
}