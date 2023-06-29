#device
使用方法
1.tp6生成一个新的命令行类

2.在新生成的类中使用扩展中的trait

3.每当有事件触发但没有相关处理函数时 会触发trait的__call方法 如果该事件需要处理 则在新生成的命令行类中添加以该事件名 命名的方法

示例如下

```
<?php
declare (strict_types = 1);

namespace app\command;

use Maidou\Holiday\Holiday;
use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Wxlmaidou\Device\Tcp;//引用扩展类的命名空间

class Hello extends Command
{

    use Tcp;

    protected function configure()
    {
        // 指令配置
        $this->setName('hello')
            ->setDescription('the hello command');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->port = 8084;// 定义tcp的端口
        $this->do();
    }




    //自定义事件同步事件触发时的方法 此处是给客户端返回信息
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
```
