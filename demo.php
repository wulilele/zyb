<?php
/**
 * Created by PhpStorm.
 * Author: wulilele <1181200882@qq.com>
 * Date: 2018/6/6
 * Time: 7:18 PM
 */
require_once "src/zyb/TicketOrder.php";
require_once "src/zyb/Order.php";
require_once "src/zyb/OrderData.php";
require_once "src/Lite.php";
$config = [
    'corpCode' => '',
    'userName' => '',
    'secret' => '',
    'notify_url' => '',
    'url' => ""
];
$ticketOrder = new Wulilele\Zhiyoubao\zyb\TicketOrder();
$ticketOrder->goodsName = "";
$ticketOrder->price = 1;
$ticketOrder->quantity = 1;
$ticketOrder->occDate = "";
$ticketOrder->totalPrice = 1;
$ticketOrder->goodsCode = "";
$ticketOrder->orderCode = "12321312312";

$ticketOrders = [];
array_push($ticketOrders,$ticketOrder);

$orderData = new Wulilele\Zhiyoubao\zyb\OrderData();
$orderData->setTicketOrders($ticketOrders);
$orderData->orderCode = "";
$orderData->certificateNo = "";
$orderData->linkMobile = "";
$orderData->linkName = "";
$orderData->payMethod = "";
$orderData->orderPrice = 1;
$orderLite = new \Wulilele\Zhiyoubao\Lite($config);
$result = $orderLite->create($orderData);
var_dump($result);

