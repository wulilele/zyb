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
     * 退款
     * @param $orderData
     */
    public function refund($orderData){
        return $this->zyb->order_refund($orderData);
    }

    /**
     * 查询
     * @param $orderData
     */
    public function query($orderData){
        return $this->zyb->order_query($orderData);
    }

    /**
     *通知
     */
    public function notify(){

    }
}