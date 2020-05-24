<?php
/**
 * 分表Model
 */
namespace RedUnicorn\SymfonyKernel\Model;

use RedUnicorn\SymfonyKernel\Model\Model;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class DivideTableModel extends Model
{
    /**
     * @var string 视图名称
     */
    public $view_name;

    /**
     * @var string 分表标记
     */
    public $divide_mark;

    /**
     * @var string 源(参照)表名称
     */
    public $refer_table_name;


    function __construct(ContainerInterface $container = null, $divide_mark, $table_name)
    {
        $this->refer_table_name = $table_name;
        $this->view_name = 'view_' . $table_name;
        $this->divide_mark = $divide_mark;

        parent::__construct($container, $this->getDivideTableName($divide_mark, $table_name));
    }

    function rewritesRule(&$rule)
    {
        $this->publicRule($rule);
    }

    function rewriteInfo(&$info)
    {
        $this->publicInfo($info);
    }

    /**
     * 公共规则--使规则可以与视图共享
     *
     * @param array $rule
     * @return mixed
     */
    function publicRule(&$rules){}

    /**
     * 公共数据封装--使数据封装可以视图共享
     *
     * @param $info
     * @return mixed
     */
    function publicInfo(&$info){}

    /**
     * 得到分表的名字
     *
     * @param $divide_mark
     * @param $table_name
     * @return string
     */
    function getDivideTableName($divide_mark, $table_name)
    {
        if (empty($divide_mark)){
            return '';
        }

        $divide_mark = explode('-', $divide_mark);

        if(is_numeric($divide_mark[0]) && is_numeric(end($divide_mark))) {
            $number = end($divide_mark);
        }else{
            $number = 0;
            foreach ($divide_mark as $make){
                $number += hexdec($make);
            }
        }

        $number = $number % 100;

        if($number == 0){
            $number = 100;
        }

        return $table_name . '_' . $number;
    }


    /**
     * 得到视图名称
     *
     * @return null|string
     */
    function getViewName()
    {
        return $this->view_name;
    }

    /**
     * 创建视图
     *
     * @param int $portion
     * @throws \Doctrine\DBAL\DBALException
     */
    function createView($portion = 100)
    {
        self::$conn->executeUpdate("drop  view  if  exists  {$this->view_name}");

        $sql = '';
        for($i = 1; $i <= $portion; $i++){
            $sql .= "SELECT {$this->get('unicorn.sql')->getTableColumnsSelect($this->refer_table_name)} FROM {$this->refer_table_name}_{$i} UNION ";
        }

        self::$conn->executeUpdate('create view ' . $this->view_name .' AS ' .  substr($sql,0,-6));
    }
}