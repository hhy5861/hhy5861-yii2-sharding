<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 2017/10/22
 * Time: 22:49
 */

namespace orders\services\order;

use Yii;
use orders\models\db\Order;
use orders\components\base\Service;
use orders\models\form\order\OrderForm;

class OrderService extends Service
{
    /**
     * 获取订单详情
     *
     * @param OrderForm $form
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getOrderInfo(OrderForm $form)
    {
        /*$sql = 'select * from tbl_order';
        $result = Yii::$app->orders->createCommand($sql)->queryOne();
        var_dump($result);exit;*/
        $query = Order::find()->where(['order_id' => $form->orderId]);

        return $query->one();
    }

    /**
     * 获取订单列表
     *
     * @param OrderForm $form
     * @return array
     */
    public function getOrderList(OrderForm $form)
    {
        $query = Order::find()->filterWhere(['order_id' => $form->orderId]);
        $query->andFilterWhere(['courier_mobile' => $form->courierMobile]);
        $query->orderBy(['order_id' => SORT_DESC]);

        return $this->pageQuery($query, Order::getDb(), $form->page, $form->size);
    }

    /**
     * 下单
     *
     * @param $data
     * @return bool
     */
    public function addOrder($data)
    {
        $model = new Order();
        $model->setAttributes($data);

        return $model->save();
    }
}