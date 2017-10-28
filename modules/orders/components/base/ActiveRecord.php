<?php

namespace orders\components\base;

use Yii;

/**
 * ActiveRecord for orders module
 */
class ActiveRecord extends \app\components\base\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Yii::$app->get('orders');
    }
}
