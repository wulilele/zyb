<?php

namespace Wulilele\Zhiyoubao\zyb;
/**
 * Created by PhpStorm.
 * Author: wulilele <1181200882@qq.com>
 * Date: 2018/6/6
 * Time: 7:18 PM
 */
class Order
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function create_order($orderData)
    {
        //子订单重组
        $ticketOrders = $orderData->ticketOrders;
        $ticketOrder = array();
        for ($i = 0; $i < count($ticketOrders); $i++) {
            $ticketOrder["ticketOrder" . "[" . $i . "]"] = $ticketOrders[$i];
        }
        $orderRequest = [
            "order" => [
                "certificateNo" => $orderData->certificateNo,
                "linkName" => $orderData->linkName,
                "linkMobile" => $orderData->linkMobile,
                "orderCode" => $orderData->orderCode,
                "orderPrice" => $orderData->orderPrice,
                "groupNo" => $orderData->groupNo,
                "subSrc" => $orderData->subSrc,
                "payMethod" => $orderData->payMethod,
                'thirdPayMethod' => $orderData->thirdPayMethod,
                'bankCode' => $orderData->bankCode,
                'bankName' => $orderData->bankName,
                "ticketOrders" => $ticketOrder
            ]
        ];
        $arr = $this->getPostParam('SEND_CODE_REQ', $orderRequest);
        return $this->curlPost($this->config['url'],$arr);
    }

    private function curlPost($url,$requestString,$timeout = 5){
        if($url == '' || $requestString == '' || $timeout <=0){
            return false;
        }
        try{
            $con = curl_init((string)$url);
            curl_setopt($con, CURLOPT_HEADER, false);
            curl_setopt($con, CURLOPT_POSTFIELDS, $requestString);
            curl_setopt($con, CURLOPT_POST,true);
            curl_setopt($con, CURLOPT_RETURNTRANSFER,true);
            curl_setopt($con, CURLOPT_TIMEOUT,(int)$timeout);
            $result = curl_exec($con);
            curl_close($con);
            $data = $this->xmlToArray($result);
            return $data;
        }catch (\HttpRequestException $exception){
            throw new \HttpRequestException('页面不见了',404);
        }
    }

    private function getPostParam($transactionName, $orderRequest)
    {
        $reqParam = [
            "transactionName" => $transactionName,
            "header" => [
                "application" => "SendCode",
                "requestTime" => date("Y-m-d H:i:s")
            ],
            "identityInfo" => [
                "corpCode" => $this->config['corpCode'],
                "userName" => $this->config['userName']
            ],
            "orderRequest" => $orderRequest
        ];
        //转换为xml格式字符串
        $result = $this->arrayToXml($reqParam);
        //添加根标签
        $xml = "<PWBRequest>" . $result . "</PWBRequest>";
        //签名
        $sign = md5("xmlMsg=" . $xml . $this->config['secret']);
        //构建post参数
        $arr = array(
            "xmlMsg" => $xml,
            "sign" => $sign
        );
        return $arr;
    }

    /**
     * 数组转xml字符串,用于智游宝下单参数处理
     * @param $arr
     * @return bool|string
     * @author yuetao
     */
    private function arrayToXml($arr)
    {
        $xml = "";
        if (!is_array($arr)) {
            return false;
        }
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $key = preg_replace('/\[\d*\]/', '', $key);
                $xml .= "<" . $key . ">" . $this->arrayToXml($value) . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . ">" . $value . "</" . $key . ">";
            }
        }
        return $xml;
    }

    /**
     * XML文档转PHP数组
     * @param $xml
     * @return mixed
     */
    private function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $val = json_decode(json_encode($xmlstring), true);
        return $val;
    }
}