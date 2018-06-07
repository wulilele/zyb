<?php
/**
 * Created by PhpStorm.
 * Author: wulilele <1181200882@qq.com>
 * Date: 2018/6/7
 * Time: 1:24 PM
 */

namespace zyb;


class OrderData
{
    /**
     * @var 下单身份证，游客可根据身份证检票入园
     */
    public $certificateNo;

    /**
     * @var 联系人
     */
    public $linkName;

    /**
     * @var 手机号
     */
    public $linkMobile;

    /**
     * @var 第三方平台订单号
     */
    public $orderCode;

    /**
     * @var 订单总价
     */
    public $orderPrice;

    /**
     * @var 团号
     */
    public $groupNo;

    /**
     * @var 订单子来源
     */
    public $subSrc;

    /**
     * @var 支付方式:spot现场支付；vm备佣金；zyb智游宝支付
     */
    public $payMethod;

    /**
     * @var 游客支付方式：weixin微信支付；alipay支付宝；cup银联
     */
    public $thirdPayMethod;

    /**
     * @var 游客支付银行卡号
     */
    public $bankCode;

    /**
     * @var 游客支付银行名称
     */
    public $bankName;

    /**
     * @var 子订单
     */
    public $ticketOrders;

    /**
     * @param array $ticketOrders 子订单对集合
     */
    public function setTicketOrders($ticketOrders)
    {
        if (!is_array($ticketOrders) || count($ticketOrders) < 1) {
            $ticketOrders = null;
        } else {
            foreach ($ticketOrders as $key => $ticketOrder){
                $ticketOrders[$key] = (array)$ticketOrders;
            }
        }
        $this->ticketOrders = $ticketOrders;
    }


}