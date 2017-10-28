<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25/10/2017
 * Time: 09:27
 */

namespace app\components\shard;

use Yii;
use yii\db\DataReader;

class Command extends \yii\db\Command
{
    const EVENT_SPLIT_DATABASE = 'beforeSplitDataBase';

    const EVENT_SPLIT_TABLES = 'beforeSplitTables';

    /**
     * @var Connection db
     */
    public $db;

    /**
     * @var Sharding Sharding
     */
    public $shardingClass = 'app\components\shard\Sharding';

    /**
     * @var string shard key
     */
    public $shardValue;

    /**
     * @return string
     */
    public function getShardValue(): string
    {
        return $this->shardValue;
    }

    /**
     * @param string $shardValue
     * @return $this
     */
    public function setShardValue(string $shardValue)
    {
        $this->shardValue = $shardValue;

        return $this;
    }

    /**
     * get shard obj
     *
     * @return Sharding
     */
    protected function getShard(): Sharding
    {
        return new $this->db->shardClass();
    }

    /**
     * @return string
     */
    public function getRawSql(): string
    {
        $sql = parent::getRawSql();
        $this->getParserSql($sql);

        return $sql;
    }

    /**
     * @param $sql
     * @return string
     */
    protected function getParserSql($sql): string
    {
        $parserSql = (new ParserSql())->setCommand($this);
        $shardValue = $parserSql->setSql($sql)->matchShardKeyValue()->getShardValue();

        $this->setShardValue($shardValue)->shard();

        return $shardValue;
    }

    /**
     * @return Sharding
     */
    public function createdShard(): Sharding
    {
        return new $this->shardingClass;
    }

    /**
     * on sharding
     */
    public function shard()
    {
        if ($this->getShardValue()) {
            $shard = $this->createdShard();
            $shard->setConn($this->db);

            $this->on(self::EVENT_SPLIT_TABLES, [$shard, self::EVENT_SPLIT_TABLES]);
            $this->on(self::EVENT_SPLIT_DATABASE, [$shard, self::EVENT_SPLIT_DATABASE]);
        }
    }

    /**
     * @param string $method
     * @param null $fetchMode
     * @return mixed|DataReader
     * @throws \yii\db\Exception
     */
    protected function queryInternal($method, $fetchMode = null)
    {
        $this->trigger(self::EVENT_SPLIT_DATABASE);
        $node = $this->createdShard()->getNode();

        $result = parent::queryInternal($method, $fetchMode);

        return $result;
    }


}