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

    /**
     * 分时库存查询
     * @param $goodsCode string 商品票型编码
     * @param $startDate string 开始日期  2017-11-28
     * @param $endDate string 结束日期 2017-11-28
     */
    public function get_store($goodsCode,$startDate,$endDate){
        $queryCondtion = [
            'goodsCode' => $goodsCode,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
        $reqParam = [
            "transactionName" => "FS_STOCK_REQ",
            "header" => [
                "application" => "SendCode",
                "requestTime" => date("Y-m-d")
            ],
            "identityInfo" => [
                "corpCode" => $this->config['corpCode'],
                "userName" => $this->config['userName']
            ],
            "queryCondtion" => $queryCondtion
        ];

        //数组去掉null
        $arr = $this->array_remove_empty($reqParam);  // filter the array

        //转换为xml格式字符串
        $result = $this->arrayToXml($arr);
        //添加根标签
        $xml = "<PWBRequest>" . $result . "</PWBRequest>";
        //签名
        $sign = md5("xmlMsg=" . $xml . $this->config['secret']);
        //构建post参数
        $arr = array(
            "xmlMsg" => $xml,
            "sign" => $sign
        );
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 下单
     * @param $orderData
     * @return mixed
     */
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
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 取消订单
     * @desc 取消订单
     * @return string transactionName 返回标识，固定值为SEND_CODE_CANCEL_RES
     * @return int code 请求状态:0成功；成功返回时会一并返回退单情况查询编号retreatBatchNo
     * @return string description 返回信息,返回code大于0时描述错误信息
     * @return string retreatBatchNo 退单情况查询编号，失败时不返回该字段
     */
    public function cancel_order($data)
    {
        $orderRequest = [
            "order" => [
                "orderCode" => $data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("SEND_CODE_CANCEL_NEW_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }


    /**
     * 检票情况查询
     * @desc 检票情况查询
     * @return string transactionName 返回标识，固定值为CHECK_STATUS_QUERY_RES
     * @return int code 请求状态:0成功；成功返回时会一并返回检票情况subOrders
     * @return string description 返回信息,返回code大于0时描述错误信息
     * @return string subOrders 检票情况subOrder
     * @return string needCheckNum 需检票数量
     * @return string alreadyCheckNum 已检票数量
     * @return string returnNun 已退票数量
     * @return string checkStatus 检票状态：un_check未检；checking开检；checked完成
     * @return string orderType 订单类型:hotel酒店；repast餐厅；scenic景区
     * @return string orderCode 订单编号
     * @return string lastCheckTime 最后一次检票时间，或者是取消订单时间，如果没有发生检票的退票，返回是空串
     */
    public function checkStatusQuery($data)
    {
        $orderRequest = [
            "order" => [
                "orderCode" =>$data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("CHECK_STATUS_QUERY_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 发码图片查询
     * @desc 发码图片查询
     * @return string transactionName 返回标识，固定值为SEND_CODE_IMG_RES
     * @return int code 请求状态:0成功；成功返回时会一并返回检票图片img
     * @return string description 返回信息,返回code大于0时描述错误信息
     * @return string img 检票图片,Base_64加密，转换时先解密再转换成byte[]
     */
    public function imgQuery($data)
    {
        $orderRequest = [
            "order" => [
                "orderCode" =>$data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("SEND_CODE_IMG_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 发短信
     * @desc 发短信
     * @return string transactionName 返回标识，固定值为SEND_SM_RES
     * @return int code 请求状态:0成功；
     * @return string description 返回信息,返回code大于0时描述错误信息
     */
    public function sendSms($data)
    {
        $orderRequest = [
            'order' => [
                'orderCode' => $data['orderCode'],
                'tplCode' => $data['tplCode']
            ]
        ];
        $arr = $this->getPostParam("SEND_SM_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 部分退票
     * @param orderCode 子订单号
     * @param returnNum 退票数量
     * @param idCards 身份证号
     * 退款
     */
    public function order_refund($data)
    {
        $orderRequest = [
            'returnTicket' => [
                'orderCode' => $data['orderCode'],
                'returnNum' => $data['returnNum'],
                'idCards' => $data['idCards']
            ]
        ];
        $arr = $this->getPostParam("RETURN_TICKET_NUM_NEW_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }


    /**
     * 获取订单检票信息
     * @desc 获取订单检票信息
     * @return string transactionName 返回标识，固定值为QUERY_SUB_ORDER_CHECK_RECORD_RES
     * @return int code 请求状态:0成功；
     * @return string description 返回信息,返回code大于0时描述错误信息
     * @return string suborders 订单检票信息集"subOrder": {"needCheckNum": "1","alreadyCheckNum": "0","returnNum": "1","checkStatus": "checked","orderCode": ""}
     * @return string suborder 订单检票信息
     * @return string needCheckNum 需检票数量
     * @return string alreadyCheckNum 已检票数量
     * @return string returnNum 退票数
     * @return string checkStatus 检票状态：un_check未见票；checked检票完成；checking检票中（有还没有消费的票）
     * @return string checkRecords 检票记录
     * @return string subOrderCheckRecord 子订单检票记录
     * @return string checkNum 每次检票的数量
     * @return string checkTime 每次检票的时间
     */
    public function subOrderCheckQuery($data)
    {
        $orderRequest = [
            'order' => [
                'orderCode' => $data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("QUERY_SUB_ORDER_CHECK_RECORD_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 到付单取消
     * @desc 到付单取消
     * @return string transactionName 返回标识，固定值为CANCEL_SPOT_ORDER_RES
     * @return int code 请求状态:0成功；
     * @return string description 返回信息,返回code大于0时描述错误信息
     */
    public function cancelSpotOrder($data){
        $orderRequest = [
            'order' => [
                'orderCode' => $data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("CANCEL_SPOT_ORDER_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 退票情况查询
     * @desc 退票情况查询
     * @return string transactionName 返回标识，固定值为QUERY_RETREAT_STATUS_RES
     * @return int code 请求状态:0成功；6未审核完成
     * @return string description 返回信息,例如"审核完成"；"审核未通过"；"等待审核中"
     */
    public function returnStatusQuery($data){
        $orderRequest = [
            'order' => [
                'retreatBatchNo' => $data['retreatBatchNo']
            ]
        ];
        $arr = $this->getPostParam("QUERY_RETREAT_STATUS_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 获取二维码链接
     * @desc 获取二维码链接
     * @return string transactionName 返回标识，固定值为QUERY_IMG_URL_RES
     * @return int code 请求状态:0成功，成功会一并返回img
     * @return string description 返回信息
     * @return string img 二维码链接，点击链接进入，获取二维码图片
     */
    public function queryImgUrl($data){
        $orderRequest = [
            'order' => [
                'orderCode' => $data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("QUERY_IMG_URL_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     * 改签
     * @desc 改签
     * @return string transactionName 返回标识，固定值为ORDER_ENDORSE_RES
     * @return int code 请求状态:0成功
     * @return string description 返回信息
     */
    public function orderEndorse($data){
        $orderRequest = [
            'endorse' => [
                'subOrderCode' => $data['subOrderCode'],
                'newOccDate' => $data['newOccDate']
            ]
        ];
        $arr = $this->getPostParam("ORDER_ENDORSE_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }

    /**
     *获取二维码短链接
     * @desc 查询二维码短链接
     * @return string transactionName 返回标识，固定值为QUERY_SHORT_IMG_URL_RES
     * @return int code 请求状态：0成功
     * @return string description 返回信息
     * @return string img 返回二维码链接
     */
    public function queryShortImgUrl($data){
        $orderRequest = [
            'order' => [
                'orderCode' => $data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("QUERY_SHORT_IMG_URL_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }


    /**
     *到付单查询
     * @desc 到付单查询
     * @return string transactionName 返回标识，固定值为QUERY_SPOT_ORDER_RES
     * @return int code 请求状态：0成功；成功会返回订单信息order
     * @return string description 返回信息
     * @return string order 订单信息
     * @return string linkName 联系人
     * @return string orderCode 订单编号
     * @return string orderStatus 到付订单状态
     * @return string assistCheckNo 辅助检票号
     * @return string goodsCode 商品编码
     * @return string startDate 开始时时间
     * @return string endDate 结束时间
     * @return string occDate 游玩时间
     * @return string scenicOrders 门票订单
     * @return string scenicThirdCode 门票第三方子订单号
     */
    public function querySpotOrder($data){
        $orderRequest = [
            'order' => [
                'orderCode' => $data['orderCode']
            ]
        ];
        $arr = $this->getPostParam("QUERY_SPOT_ORDER_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }



    /**
     * 订单查询
     * @param $orderCode string 订单号
     * @return mixed
     */
    public function order_query($orderCode)
    {
        $orderRequest = [
            'order' => [
                'orderCode' => $orderCode
            ]
        ];
        $arr = $this->getPostParam("QUERY_ORDER_NEW_REQ", $orderRequest);
        $result = $this->curlPost($this->config['url'], $arr);
        return $result;
    }


    /**
     *智游宝通知处理
     * @param $param array $_GET参数
     * @param $config array 配置
     * @desc 通知转发接收方需验证签名，成功收到消息验证成功回复json格式status:success,错误回复error
     * @return array
     */
    public function notify($param,$config){
        if(!$this->checkSign($param,$config)){
           exit();
        }
        return $param;
    }


    /**
     * 智游宝回调签名验证
     * @param $param
     * @return bool
     */
    private function checkSign($param,$config){

        if(array_key_exists('sign',$param) && array_key_exists('orderCode', $param)){

            $str =$param['orderCode'].$config['secret'];
            $e = md5($str);
            $sign = $param['sign'];
            if($e == $sign){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }





    private function curlPost($url, $requestString, $timeout = 5)
    {
        if ($url == '' || $requestString == '' || $timeout <= 0) {
            return false;
        }
        $o = "";
        foreach ($requestString as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);
        try {
            $con = curl_init((string)$url);
            curl_setopt($con, CURLOPT_HEADER, false);
            curl_setopt($con, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($con, CURLOPT_POST, true);
            curl_setopt($con, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($con, CURLOPT_TIMEOUT, (int)$timeout);
            $result = curl_exec($con);
            curl_close($con);
            $data = $this->xmlToArray($result);
            return $data;
        } catch (\HttpRequestException $exception) {
            throw new \HttpRequestException('页面不见了', 404);
        }
    }

    private function getPostParam($transactionName, $orderRequest)
    {
        $reqParam = [
            "transactionName" => $transactionName,
            "header" => [
                "application" => "SendCode",
                "requestTime" => date("Y-m-d")
            ],
            "identityInfo" => [
                "corpCode" => $this->config['corpCode'],
                "userName" => $this->config['userName']
            ],
            "orderRequest" => $orderRequest
        ];

        //数组去掉null
        $arr = $this->array_remove_empty($reqParam);  // filter the array

        //转换为xml格式字符串
        $result = $this->arrayToXml($arr);
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

    /**
     * 递归去掉数组中null的值
     * @param $haystack
     * @return mixed
     */
    private function array_remove_empty($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->array_remove_empty($haystack[$key]);
            }
            if (empty($value)) {
                unset($haystack[$key]);
            }
        }
        return $haystack;
    }
}