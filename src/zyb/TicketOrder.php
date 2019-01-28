<?php
/**
 * Created by PhpStorm.
 * Author: wulilele <1181200882@qq.com>
 * Date: 2018/6/7
 * Time: 1:35 PM
 */

namespace Wulilele\Zhiyoubao\zyb;


class TicketOrder
{
    /**
     * @var  子订单号
     */
    public $orderCode;

    /**
     * @var 票价
     */
    public $price;

    /**
     * @var 数量
     */
    public $quantity;

    /**
     * @var 子订单总价
     */
    public $totalPrice;

    /**
     * @var 游玩日期
     */
    public $occDate;

    /**
     * @var 商品编码，同票型编码
     */
    public $goodsCode;

    /**
     * @var 商品名称，非必须
     */
    public $goodsName;

    /**
     * @var string 分时票开始时间
     */
    public $fsStartTime;

    /**
     * @var string 分时票结束时间
     */
    public $fsEndTime;

    /**
     * @var 备注，非必须
     */
    public $remark;

    public $credentials;

    /**
     * @param array $credentials
     */
    public function setCredentials($credentials)
    {
        $arr['credential']['id'] = $credentials['id_number'];
        $arr['credential']['name'] = $credentials['name'];
        $this->credentials = $arr;
    }




}