<?php
// session_start();
/* 
 * 黎明互联
 * https://www.liminghulian.com/
 */

class  Base
{
    const KEY = 'myxk2018myxk2018myxk2018myxk2018'; //请修改为自己的
    const MCHID = '1500198842'; //请修改为自己的
    const RPURL = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
    const APPID = 'wxbfcd9a63fe00e813';//请修改为自己的
    const CODEURL = 'https://open.weixin.qq.com/connect/oauth2/authorize?';
    const OPENIDURL = 'https://api.weixin.qq.com/sns/oauth2/access_token?';
    const SECRET = 'e8e0fcb2d4eb2509e8e3fc1ecb51c54a';//请修改为自己的
    //获取用户openid 为避免重复请求接口获取后应做存储
    public function init()
    {
        // parent::init();
    }
	/**  
	* 获取签名 
	* @param array $arr
	* @return string
	*/  
    public function getSign($arr){
        //去除空值
        $arr = array_filter($arr);
        if(isset($arr['sign'])){
            unset($arr['sign']);
        }
        //按照键名字典排序
        ksort($arr);
        //生成url格式的字符串
       $str = $this->arrToUrl($arr) . '&key=' . self::KEY;
       // var_dump($str );exit;
       return strtoupper(md5($str));
    }
    /**  
	* 获取带签名的数组 
	* @param array $arr
	* @return array
	*/  
    public function setSign($arr){
        $arr['sign'] = $this->getSign($arr);
        return $arr;
    }
	/**  
	* 数组转URL格式的字符串
	* @param array $arr
	* @return string
	*/
    public function arrToUrl($arr){
        return urldecode(http_build_query($arr));
    }
    
    //数组转xml
    function ArrToXml($arr)
    {
            if(!is_array($arr) || count($arr) == 0) return '';

            $xml = "<xml>";
            foreach ($arr as $key=>$val)
            {
                    if (is_numeric($val)){
                            $xml.="<".$key.">".$val."</".$key.">";
                    }else{
                            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
                    }
            }
            $xml.="</xml>";
            return $xml; 
    }
	
    //Xml转数组
    function XmlToArr($xml)
    {	
            if($xml == '') return '';
            libxml_disable_entity_loader(true);
            $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
            return $arr;
    }
    function postData($url,$postfields){
       
        $ch = curl_init();
        $params[CURLOPT_URL] = $url;    //请求url地址
        $params[CURLOPT_HEADER] = false; //是否返回响应头信息
        $params[CURLOPT_RETURNTRANSFER] = true; //是否将结果返回
        $params[CURLOPT_FOLLOWLOCATION] = true; //是否重定向
        $params[CURLOPT_POST] = true;
        $params[CURLOPT_POSTFIELDS] = $postfields;
        $params[CURLOPT_SSL_VERIFYPEER] = false;
        $params[CURLOPT_SSL_VERIFYHOST] = false;
        //以下是证书相关代码
             $params[CURLOPT_SSLCERTTYPE] = 'PEM';
             $params[CURLOPT_SSLCERT] = '/var/www/html/psy/protected/components/wxCom/cert/apiclient_cert.pem';
             $params[CURLOPT_SSLKEYTYPE] = 'PEM';
             $params[CURLOPT_SSLKEY] = '/var/www/html/psy/protected/components/wxCom/cert/apiclient_key.pem';

          curl_setopt_array($ch, $params); //传入curl参数
          // $content = curl_exec($ch); //执行
          //运行curl
        $data = curl_exec($ch);
        //返回结果
        if ($data) {
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            var_dump($error);exit;
            throw new WxPayException("curl出错，错误码:$error");
        }
    }
}