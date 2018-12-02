<?php
/**
 * Created by Qiang.
 * User: Fuqiang Wang
 * Date: 2018/12/2
 * Time: 13:08
 * 微信二维码支付
 * 神灯智能短信微信充值
 * Thinkphp 5.0
 */

namespace app\admin\controller;
use app\admin\model\JoinSmsRegModel;
use think\Db;

class WeixinPay extends Admin
{

    //获取短信充值支付的二维码
    public function getQrCode()
    {
        $data = input('param.');

        $result = $this->validate($data, 'WeixinPay.getQrCode');
        if (true !== $result) {
            // 验证失败 输出错误信息
            $this->getError($result);
        }


        $SMS = [
            'A' => [
                "name" => "A套餐",
                "price" => 59,
                "num" => 1000,
            ],
            'B' => [
                "name" => "B套餐",
                "price" => 112,
                "num" => 2000,
            ],
            'C' => [
                "name" => "C套餐",
                "price" => 260,
                "num" => 5000,
            ],
            'D' => [
                "name" => "D套餐",
                "price" => 490,
                "num" => 10000,
            ],
            'E' => [
                "name" => "E套餐",
                "price" => 900,
                "num" => 20000,
            ],
        ];

        $goods = $SMS[$data['type']];


        //生成订单记录
        $JoinSmsRegModel = new JoinSmsRegModel();

        $ordernum = $JoinSmsRegModel->getOrdernum();

        $inData = [
            'join_code' => $data['join_code'],
            'price' => $goods['price'],
            'nums' => $goods['num'],
            'create_time' => time(),
            'update_time' => time(),
            'ordernum' => $ordernum
        ];
        $JoinSmsRegModel->save($inData);


        $appid = "wxb21cedf98fc168d4";
        $apikey = "7ba9055b171635a298f406be5233c99f";
        $mch_id = "1501374621";
        $body = $goods['name'];
        $order_id = $ordernum;
        $money = $goods['price'];
        $notify_url = config('domain') . '/admin.php/Weixin_Notify/notify.html';

        $this->pay($appid, $apikey, $mch_id, $body, $order_id, $money, $notify_url);
        $this->getSuccess($this->pay($appid, $apikey, $mch_id, $body, $order_id, $money, $notify_url));
    }


    /**
     * @param $appid
     * @param $apikey
     * @param $openid
     * @param $mch_id
     * @param $body
     * @param $order_id
     * @param $money
     * @param $notify_url
     * @return bool
     */
    public function pay($appid, $apikey, $mch_id, $body, $order_id, $money, $notify_url)
    {
        //获得支付的参数
        $rand = md5(time() . rand(1000, 9999));
        $param["appid"] = $appid;
        //$param["openid"] = $openid;
        $param["mch_id"] = $mch_id; //商户ID
        $param["nonce_str"] = $rand;
        $param["body"] = $body;
        $param["out_trade_no"] = $order_id; //订单编号，要保证不重复
        $param["total_fee"] = $money * 100; //支付金额
        $param["spbill_create_ip"] = $_SERVER["REMOTE_ADDR"];
        $param["notify_url"] = $notify_url;
        $param["trade_type"] = "NATIVE";
        //$param['attach']  = $jump;

        ksort($param);
        $signStr = $this->ToUrlParams($param);
        //$signStr = 'appid='.$param["appid"]."&body=".$param["body"]."&mch_id=".$param["mch_id"]."&nonce_str=".$param["nonce_str"]."&notify_url=".$param["notify_url"]."&out_trade_no=".$param["out_trade_no"]."&spbill_create_ip=".$param["spbill_create_ip"]."&total_fee=".$param["total_fee"]."&trade_type=".$param["trade_type"];

        $signStr = $signStr . "&key=" . $apikey; //apikey

        $param["sign"] = strtoupper(MD5($signStr));
        $data = '<xml>
					  <appid><![CDATA[' . $param["appid"] . ']]></appid>
					  <mch_id>' . $param["mch_id"] . '</mch_id>
					  <nonce_str><![CDATA[' . $param["nonce_str"] . ']]></nonce_str>
					  <body><![CDATA[' . $param["body"] . ']]></body>
					  <out_trade_no><![CDATA[' . $param["out_trade_no"] . ']]></out_trade_no>
					  <total_fee>' . $param["total_fee"] . '</total_fee>
					  <spbill_create_ip><![CDATA[' . $param["spbill_create_ip"] . ']]></spbill_create_ip>
					  <notify_url><![CDATA[' . $param["notify_url"] . ']]></notify_url>
					  <trade_type><![CDATA[' . $param["trade_type"] . ']]></trade_type>
					  <sign><![CDATA[' . $param["sign"] . ']]></sign>
					</xml>';

        $postResult = $this->myCurl("https://api.mch.weixin.qq.com/pay/unifiedorder", $data);
        $postObj = simplexml_load_string($postResult, 'SimpleXMLElement', LIBXML_NOCDATA);

        $msg = "" . $postObj->return_msg;


        if ($msg == "OK") {
            $result["code_url"] = "" . $postObj->code_url;
            //$result["nonceStr"] = "".$postObj->nonce_str;  //不加""拿到的是一个json对象
            //$result["package"] = "prepay_id=".$postObj->prepay_id;
            //$result["signType"] = "MD5";
            //$paySignStr = 'appId='.$appid.'&nonceStr='.$result["nonceStr"].'&package='.$result["package"].'&signType='.$result["signType"].'&timeStamp='.$result["timestamp"];
            //$paySignStr = $paySignStr."&key=".$apikey;
            //$result["paySign"] = strtoupper(MD5($paySignStr));
            return $result;
        } else {
            return false;
        }
    }


    public function myCurl($url, $data)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tmpInfo = curl_exec($ch);

        if (curl_errno($ch)) {
            return $tmpInfo;
        }
        curl_close($ch);
        return $tmpInfo;
    }


    /**
     *
     * 参数数组转换为url参数
     * @param array $urlObj
     */
    private function ToUrlParams($urlObj)
    {
        $buff = "";
        foreach ($urlObj as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        $buff = trim($buff, "&");
        return $buff;
    }


}