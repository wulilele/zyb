<?php
namespace Wulilele\Zhiyoubao;
use Wulilele\Zhiyoubao\zyb\Order;
use Wulilele\Zhiyoubao\zyb\OrderData;

/**
 * Created by PhpStorm.
 * Author: wulilele <1181200882@qq.com>
 * Date: 2018/6/6
 * Time: 7:17 PM
 */
class Lite
{
    /**
     * @var Order 智游宝实例
     */
    protected $zyb;

    /**
     * Lite constructor.
     * @param $config array 配置
     */
    public function __construct($config)
    {
        $this->zyb = new Order($config);
    }

    /**
     * 下单
     * @param $orderData OrderData 订单数据
     * @return bool|mixed
     */
    public function create($orderData){
       return $this->zyb->create_order($orderData);
    }

    /**
     * 订单查询
     * @param $orderData
     */
    public function query($orderData){
        return $this->zyb->order_query($orderData);
    }

    /**
     * 部分退票
     * @param $orderData
     */
    public function refund($orderData){
        return $this->zyb->order_refund($orderData);
    }

    /**
     * 取消订单
     * @param $orderData
     * @return string
     */
    public function cancel($orderData){
        return $this->zyb->cancel_order($orderData);
    }

    /**
     * 检票情况查询
     * @param $orderData
     * @return string
     */
    public function checkStatus($orderData)
    {
        return $this->zyb->checkStatusQuery($orderData);
    }

    /**
     * 发码图片查询
     * @param $orderData
     * @return string
     */
    public function img($orderData)
    {
        return $this->zyb->imgQuery($orderData);
    }

    /**
     * 发送短信
     * @param $orderData
     * @return string
     */
    public function sendSms($orderData)
    {
        return $this->zyb->sendSms($orderData);
    }

    /**
     * 获取订单检票信息
     * @param $orderData
     * @return string
     */
    public function subOrderCheck($orderData)
    {
        return $this->zyb->subOrderCheckQuery($orderData);
    }

    /**
     * 退票情况查询
     * @param $orderData
     * @return string
     */
    public function returnStatus($orderData){
        return $this->zyb->returnStatusQuery($orderData);
    }

    /**
     * 获取二维码链接
     * @param $orderData
     * @return string
     */
    public function queryImgUrl($orderData){
        return $this->zyb->queryImgUrl($orderData);
    }

    /**
     * 获取二维码短链接
     * @param $orderData
     * @return string
     */
    public function queryShortImgUrl($orderData){
        return $this->zyb->queryShortImgUrl($orderData);
    }


    /**
     *通知签名验证
     * @param  $param array $_GET参数
     * @param $config array 配置
     */
    public function notify($param,$config){
        return $this->zyb->notify($param,$config);
    }
}