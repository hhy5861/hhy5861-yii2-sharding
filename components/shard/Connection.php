<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 24/10/2017
 * Time: 23:08
 */

namespace app\components\shard;

use yii\db\Connection as BaseConnection;

class Connection extends BaseConnection
{
    /**
     * @var array db node list
     */
    public $nodes = [];

    /**
     * @var string db shard key
     */
    public $dbShardKey = 'id';

    /**
     * @var string shard key table
     */
    public $tableShardKey = 'id';

    /**
     * @var string shard key
     */
    protected static $shardValue;

    /**
     * @var string Command class
     */
    public $commandClass = 'app\components\shard\Command';

    /**
     * @event Event an event that is triggered after a DB connection is established
     */

    /**
     * @return mixed
     */
    public function getDbShardKey()
    {
        return $this->dbShardKey;
    }

    /**
     * @param mixed $dbShardKey
     */
    public function setDbShardKey($dbShardKey)
    {
        $this->dbShardKey = $dbShardKey;
    }

    /**
     * @return mixed
     */
    public function getTableShardKey()
    {
        return $this->tableShardKey;
    }

    /**
     * @param mixed $tableShardKey
     */
    public function setTableShardKey($tableShardKey)
    {
        $this->tableShardKey = $tableShardKey;
    }
}
