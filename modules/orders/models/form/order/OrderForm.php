<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 01/09/2017
 * Time: 13:41
 */

namespace orders\models\form\order;

use app\components\base\Form;

class OrderForm extends Form
{
    /**
     * @var integer 用户id
     */
    public $id;

    /**
     * @var integer 当前页码
     */
    public $page = 1;

    /**
     * @var integer 每页返回记录数
     */
    public $size = 20;

    /**
     * @var string 订单id
     */
    public $orderId;

    /**
     * @var string 快递员手机号
     */
    public $courierMobile;


    /**
     *  获取用户信息
     */
    const GET_ORDER_INFO = 'getOrderInfo';

    /**
     *  获取订单列表
     */
    const GET_ORDER_LIST = 'getOrderList';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['id', 'integer', 'min' => 1, 'max' => 11, 'on' => [self::GET_ORDER_INFO]],
            ['page', 'integer', 'min' => 1, 'on' => [self::GET_ORDER_LIST]],
            ['size', 'integer', 'max' => 1000, 'on' => [self::GET_ORDER_LIST]],
            ['orderId', 'string', 'on' => [self::GET_ORDER_INFO, self::GET_ORDER_LIST]],

            [['id', 'orderId'], 'requiredIn', 'on' => [self::GET_ORDER_LIST]],

            ['courierMobile', 'string', 'on' => [self::GET_ORDER_INFO, self::GET_ORDER_LIST]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            self::GET_ORDER_LIST => ['id', 'page', 'size', 'orderId', 'courierMobile'],
            self::GET_ORDER_INFO => ['id', 'orderId', 'courierMobile'],
        ];
    }
}
