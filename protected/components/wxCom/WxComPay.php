<?php
include 'Base.php';
include 'Rsa.php';
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */
class WxComPay extends Base
{
    private $params;
    const PAYURL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
    const SEPAYURL = "https://api.mch.weixin.qq.com/mmpaymkttransfers/gettransferinfo";
    const PKURL = "https://fraud.mch.weixin.qq.com/risk/getpublickey";
    const BANKPAY = "https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank";
    public function getPuyKey(){
        $this->params = [
            // 'appid'=>self::APPID,
            'mch_id'    => self::MCHID,//商户ID
            'nonce_str' => md5(time()),
            'sign_type' => 'MD5'
        ];
        // var_dump($this->params);exit;
         //将数据发送到接口地址
        return $this->send(self::PKURL);
    }

    public function init()
    {
        parent::init();
    }
    public function comPay($data){
        //构建原始数据
        $this->params = [
            'mch_appid'         => self::APPID,//APPid,
            'mchid'             => self::MCHID,//商户号,
            'nonce_str'         => md5(time()), //随机字符串
            'partner_trade_no'  => date('YmdHis'), //商户订单号
            'openid'            => $data['openid'], //用户openid
            'check_name'        => 'NO_CHECK',//校验用户姓名选项 NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名
            //'re_user_name'    => '',//收款用户姓名  如果check_name设置为FORCE_CHECK，则必填用户真实姓名
            'amount'            => $data['price'],//金额 单位分
            'desc'              => '测试付款',//付款描述
            'spbill_create_ip'  => $_SERVER['SERVER_ADDR'],//调用接口机器的ip地址
        ];
        //将数据发送到接口地址
        return $this->send(self::PAYURL);
    }
    public function bankPay($data){
        $this->params = [
            'mch_id'    => self::MCHID,//商户号
            'partner_trade_no'   => date('YmdHis'),//商户付款单号
            'nonce_str'           => md5(time()), //随机串
            'enc_bank_no'         => $data['enc_bank_no'],//收款方银行卡号RSA加密
            'enc_true_name'       => $data['enc_true_name'],//收款方姓名RSA加密
            'bank_code'           => $data['bank_code'],//收款方开户行
            'amount'              => $data['amount'],//付款金额
        ];
         //将数据发送到接口地址
        return $this->send(self::BANKPAY);
    }
    public function searchPay($oid){
        $this->params = [
            'nonce_str'  => md5(time()),//随机串
            'partner_trade_no'  => $oid, //商户订单号
            'mch_id'  => self::MCHID,//商户号
            'appid'  => self::APPID //APPID
        ];
         //将数据发送到接口地址
        return $this->send(self::SEPAYURL);
    }
    public function sign(){
        return $this->setSign($this->params);
    }
    public function send($url){
        $res = $this->sign();
        // var_dump($res);exit;
        $xml = $this->ArrToXml($res);
         // var_dump($url,$xml);exit;
       $returnData = $this->postData($url, $xml);

       return $this->XmlToArr($returnData);
    }

    public function sendCom($no='',$name='',$code='',$price='')
    {
        $rsa = new RSA(file_get_contents('/var/www/html/psy/protected/components/wxCom/cert/newpubkey.pem'), '');
        $data = [
             'enc_bank_no'         => $rsa->public_encrypt($no),//收款方银行卡号RSA加密
             'enc_true_name'       => $rsa->public_encrypt($name),//收款方姓名RSA加密
             'bank_code'           => $code,//收款方开户行
             'amount'              => $price,//付款金额
        ];

        $res = $this->bankPay($data);
        return $res;
    }

}

// $obj = new WxComPay();
/* 
 * 付款到零钱
 */
// $data = [
//   'openid'  => 'oPNkhv1TzPiJ3FGau0_1MHIfnpB4',
//   'price'   => '100'
// ];
// $res = $obj->comPay($data);

//查询
/*
$oid = "20180129201209";
$res = $obj->searchPay($oid);
 * 
 */

//获取公钥

// $res = $obj->getPuyKey();
// file_put_contents('D:\xamp\htdocs\psy\protected\components\wxCom\cert\pubkey.pem', $res['pub_key']);
//  * 
 
// //企业付款到银行卡

// $rsa = new RSA(file_get_contents('D:\xamp\htdocs\psy\protected\components\wxCom\cert\newpubkey.pem'), '');
// $data = [
//      'enc_bank_no'         => $rsa->public_encrypt('1234342343234234'),//收款方银行卡号RSA加密
//      'enc_true_name'       => $rsa->public_encrypt('李明'),//收款方姓名RSA加密
//      'bank_code'           => '1002',//收款方开户行
//      'amount'              => '100',//付款金额
// ];

// $res = $obj->bankPay($data);
//  * 
 
// echo '<pre>';
// print_r($res);
