<?php
namespace Wulilele\Zhiyoubao;
use Wulilele\Zhiyoubao\zyb\Order;

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
     * @param $orderData 订单数据
     */
    public function create($orderData){
       return $this->zyb->create_order($orderData);
    }
}