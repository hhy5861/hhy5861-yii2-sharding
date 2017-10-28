<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25/10/2017
 * Time: 09:36
 */

namespace app\components\shard;

use yii\base\Component;
use Flexihash\Flexihash;

class Sharding extends Component
{
    public $split;

    public $node;

    protected static $conn;

    protected $point;

    protected $_node = [];

    protected $config = [];

    /**
     * @return Connection
     */
    public function getConn(): Connection
    {
        return self::$conn;
    }

    /**
     * @param Connection $conn
     * @return $this
     */
    public function setConn(Connection $conn)
    {
        self::$conn = $conn;

        return $this;
    }

    /**
     * node array
     */
    public function nodeGroup()
    {
        $nodes = $this->getConn()->nodes;
        $this->_node = array_keys($nodes);
    }

    /**
     * one-time split database point
     *
     * @return string
     * @throws SharDbException
     */
    public function beforeSplitDataBase()
    {
        $this->nodeGroup();

        $hash = new Flexihash();
        $hash->addTargets($this->_node);

        $point = $hash->lookup($this->getConn()->getShardValue());
        if (!isset($this->_node[$point])) {
            throw new SharDbException('DB Can not find configuration point');
        }

        $this->setNode($this->_node[$point]);
        $this->setPoint($point);

        var_dump($point);exit;
        return $point;
    }

    public function beforeSplitTables()
    {

    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param mixed $point
     */
    public function setPoint($point)
    {
        $this->point = $point;
    }

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param mixed $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }


}
