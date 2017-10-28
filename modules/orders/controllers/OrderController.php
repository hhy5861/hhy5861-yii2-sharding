<?php

namespace orders\controllers;

use Yii;
use yii\helpers\Json;
use app\components\base\Response;
use orders\components\base\Controller;
use orders\services\order\OrderService;
use app\components\helpers\ArrayHelper;
use orders\models\form\order\OrderForm;

/**
 * 订单模块
 */
class OrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function verbs()
    {
        return [
            'add-order' => ['post'],
            'get-order-info' => ['get'],
            'get-order-list' => ['get'],
        ];
    }

    /**
     * 获取订单信息
     *
     * @return Response
     */
    public function actionGetOrderInfo()
    {
        $param = Yii::$app->request->get();

        $form = new OrderForm();
        $form->validateScenario($param, 'getOrderInfo');

        $result = OrderService::getInstance()->getOrderInfo($form);
        return new Response(['data' => $result]);
    }

    /**
     * 获取订单列表
     *
     * @return Response
     */
    public function actionGetOrderList()
    {
        $param = Yii::$app->request->get();

        $form = new OrderForm();
        $form->validateScenario($param, 'getOrderList');

        $result = OrderService::getInstance()->getOrderList($form);
        return new Response(['data' => $result]);
    }

    /**
     * 下订单
     * 
     * @return Response 
     */
    public function actionAddOrder()
    {
        $param = Yii::$app->request->post();
        if (!$param) {
            $param = Yii::$app->request->getRawBody();
            $param = Json::decode($param);
        }

        $param['ip'] = Yii::$app->request->getUserIP();

        return new Response(['data' => $param]);
    }
}
