<?php
/**
 * Created by PhpStorm.
 * User: rebie
 * Date: 16-3-14
 * Time: 上午9:45
 */

$hook_server_name = "亢龙有悔";
$your_mobile = "17092080427";
$your_email = "main@rebiekong.com";
while(check_server($hook_server_name)){
    sleep(1);
}

function check_server($hook_server_name)
{
    $data = file_get_contents("http://jx3gc.autoupdate.kingsoft.com/jx3gc/zhcn/serverlist/serverlist.ini?t=".time());
    $data = iconv("gb2312","utf-8",$data);
    $servers_string = explode(PHP_EOL,$data);
    echo date("Y-m-d H:i:s").":";
    foreach ($servers_string as $server_string) {
        $temp_array = explode("	", $server_string);
        if (count($temp_array) == 10) {
            if ($hook_server_name == $temp_array[1]) {
                if ($temp_array[2] != 3) {
                    echo $hook_server_name." is ready".PHP_EOL;
                    notice_ready($hook_server_name);
                    return false;
                } else {
                    echo $hook_server_name." is not ready".PHP_EOL;
                    return true;
                }
                break;
            }
        }
    }
    echo "data error";
}

function notice_ready($server_name){
    global $your_email,$your_mobile;
    notice_ready_mail($server_name,$your_email);
    notice_ready_mail($server_name,$your_mobile);
}

function notice_ready_mail($server_name,$email) {
    /**
     * TODO 配置你的SMTP信息
     */
    $host = "";
    $account = "";
    $password = "";
    $your_mail = "";

    include_once __DIR__."/Smtp.php";
    $smtp = new Smtp($host,$account,$password,465,true);
    $smtp->setFrom($your_mail);
    $smtp->setReceiver($email);
    $smtp->setMail("开服通知","你选择的服务器 {$server_name} 已经开启");//，开服时间：".date("Y-m-d H:i:s"));
    $smtp->sendMail();
}



function notice_ready_sms($server_name,$mobile) {
    /**
     * TODO 配置你的阿里大鱼信息
     * @link http://www.alidayu.com/
     */
    $appkey = "";
    $secretKey = "";
    $your_sign = "";
    $templateCode = "";

    include_once __DIR__."/ali/TopSdk.php";
	$c = new TopClient;
	$c->appkey = $appkey;
	$c->secretKey = $secretKey;
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setSmsType("normal");
	$req->setSmsFreeSignName($your_sign);
	$req->setSmsParam('{"server":"'.$server_name.'"}');
	$req->setRecNum($mobile);
	$req->setSmsTemplateCode($templateCode);
	$c->execute($req);
}