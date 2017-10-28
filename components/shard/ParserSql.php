<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 25/10/2017
 * Time: 16:57
 */

namespace app\components\shard;

class ParserSql
{
    /**
     * @var string sql
     */
    public $sql;

    /**
     * @var string match pattern
     */
    protected $pattern;

    /**
     * @var string shard key value
     */
    protected $shardValue = '';

    /**
     * @var Command;  obj
     */
    protected static $command;

    /**
     * @var array matche return result
     */
    protected $matche;

    /**
     * @return Command
     */
    public function getCommand(): Command
    {
        return self::$command;
    }

    /**
     * @param Command $command
     * @return ParserSql
     */
    public function setCommand(Command $command):ParserSql
    {
        self::$command = $command;

        return $this;
    }

    /**
     * @return string
     */
    public function getSql(): string
    {
        return $this->sql;
    }

    /**
     * @param string $sql
     * @return ParserSql
     */
    public function setSql(string $sql): ParserSql
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * @param string $shardKey
     * @return $this
     */
    public function setPattern(string $shardKey): ParserSql
    {
        $this->pattern = "/(`?)order_id\\1\s*=\s*(\\'?)(?P<{$shardKey}>\d+)\\2/";

        return $this;
    }

    /**
     * @return $this
     */
    protected function matchParserSql()
    {
        preg_match($this->getPattern(), $this->getSql(), $this->matche);
    }

    /**
     * @return ParserSql
     */
    public function matchShardKeyValue(): ParserSql
    {
        $sharKey = $this->getCommand()->db->getTableShardKey();
        $this->setPattern($sharKey)->matchParserSql();
        if ($this->matche) {
            $this->setShardValue(array_pop($this->matche));
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getShardValue(): string
    {
        return $this->shardValue;
    }

    /**
     * @param string $shardValue
     */
    public function setShardValue(string $shardValue)
    {
        $this->shardValue = $shardValue;
    }
}