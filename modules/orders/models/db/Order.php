<?php

namespace orders\models\db;

use orders\components\base\ActiveRecord;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $brand
 * @property string $order_id
 * @property integer $courier_id
 * @property string $courier_code
 * @property string $courier_mobile
 * @property string $courier_name
 * @property string $sender_uid
 * @property integer $shop_id
 * @property string $shop_code
 * @property string $shop_name
 * @property string $order_ids
 * @property string $create_time
 * @property string $update_time
 */
class Order extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courier_id', 'shop_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['brand', 'courier_code', 'courier_mobile', 'shop_code'], 'string', 'max' => 20],
            [['order_id'], 'string', 'max' => 100],
            [['courier_name', 'shop_name'], 'string', 'max' => 30],
            [['sender_uid'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'brand' => '快递品牌',
            'order_id' => '订单id',
            'courier_id' => '业务员id',
            'courier_code' => '业务员编号',
            'courier_mobile' => '业务员手机号',
            'courier_name' => '业务员姓名',
            'sender_uid' => '发件人用户id',
            'shop_id' => '网点id',
            'shop_code' => '网点编号',
            'shop_name' => '网点名',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
