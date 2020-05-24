<?php
/**
 * 实体类父类
 *
 * Jay
 *
 * 子类可对其方法在功能不变的情况下进行重写
 *
 *
 */
namespace RedUnicorn\SymfonyKernel\Model;

use Doctrine\Common\Persistence\ObjectManager;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Responses\Responses;
use RedUnicorn\SymfonyKernel\Model\Rule\Rule;
use RedUnicorn\SymfonyKernel\Model\Rule\Rules;
use Symfony\Component\DependencyInjection\ContainerInterface;
use BaseBundle\Controller\BaseController;

abstract class Model extends BaseController
{
    const OM_ALL = 0;  //输出模式   全部模式数据
    const OM_COUNT = 1;  //输出模式  返回查询总数[不分页]
    const OM_ALL_LIST = 2;  //输出模式  返回查询全部的数据
    const OM_LIMIT_LIST = 3;  //输出模式 返回分页全部的数据
    const OM_SQL = 4;  //输出模式 返回查询的SQL语句
    const OM_SQL_ARRAY = 5; //输出模式 返回查询SQL语句的组成部分数组

    const R_SELECT = 'select';  //默认支持规则select  自定义查询字段 参数格式 string
    const R_ORDER_BY = 'order_by';  //默认支持规则order_by  自定义优先排序方式 （支持一维数组和字符串） 单个数组中为字符串  条件会自动拼接到sql中 参数开头必须是 ASC DESC
    const R_WHERE = 'where'; //默认支持规则where 自定义查询条件 数组（支持一维数组和字符串） 单个数组中为字符串  条件会自动拼接到sql中 参数开头必须是 AND 或者 OR
    const R_JOIN = 'join'; //默认支持规则json 自定义join （支持一维数组和字符串） 单个数组中为字符串

    public $sql = '';

    public $table_name = null;

    public $lastInsertId = null;

    private $supplement_where = '';

    /**
     * @var ObjectManager
     */
    protected $entityManager = '';

    protected $tel_sql_array = array(
        'pre' => 't',    //主表前缀
        'select' => '',
        'from' => '',
        'join' => '',
        'where' => '',
        'order_by' => '',
        'final_order_by' => '.id DESC ', //最终排序
        'primary_key' => 'id'  //用于getInfo查询使用
    );

    public $sql_array = array(
        'pre' => '',
        'select' => '',
        'from' => '',
        'join' => '',
        'where' => '',
        'order_by' => '',
        'final_order_by' => '', //最终排序
        'primary_key' => ''  //用于getInfo查询使用
    );

    public function __construct(ContainerInterface $container = null, $table_name = null)
    {
        $this->table_name = $table_name;
        parent::__construct($container);
    }

    ###########TODO 新增编辑

    /**
     * 验证
     *
     * @param $data
     * @param bool $id
     * @return boolean
     */
    abstract function validate($data, $id = false);

    /**
     * 新增
     *
     * @param $data
     * @return boolean
     */
    abstract function insert($data);

    /**
     * 编辑
     *
     * @param $id
     * @param $data
     * @return boolean
     */
    abstract function  update($id ,$data);


    ###########TODO 查询

    public function getAllList($rules = array())
    {
        return $this->getList($rules, self::OM_ALL_LIST)['info'];
    }

    public function getLimitList($rules = array())
    {
        return $this->getList($rules, self::OM_LIMIT_LIST)['info'];
    }

    public function getCount($rules = array())
    {
        return $this->getList($rules, self::OM_COUNT)['count'];
    }

    public function getSql($rules = array())
    {
        return $this->getList($rules, self::OM_SQL)['sql'];
    }

    public function getSqlArray($rules = array())
    {
        return $this->getList($rules, self::OM_SQL_ARRAY)['sql_array'];
    }

    public function getId($rules = array(), $def = false)
    {
        return $this->getColumn('sql_pre.' . $this->tel_sql_array['primary_key'] , $rules, $def);
    }

    public function getLastId($rules = array(), $def = false)
    {
        $this->setFunctionRule($rules, 'last');

        return $this->getId($rules, $def);
    }

    public function getLastColumn($column, $rules = array(), $def = false)
    {
        $this->setFunctionRule($rules, 'last');

        return $this->getColumn($column, $rules, $def);
    }

    /**
     * @param string $model simple_array | array
     */
    public function getIds($rules = array(), $model = 'simple_array')
    {
        $rules = new Rules($rules);
        $rules->addRule(new Rule(Model::R_SELECT, 'sql_pre.' . $this->tel_sql_array['primary_key'] . ' as id', Rule::REPLACE));
        $array = array_column($this->getAllList($rules), 'id');
        if ($model == 'simple_array') {
            return implode(',', $array);
        } else {
            return $array;
        }
    }

    public function getLastAssoc($rules = array())
    {
        $this->setFunctionRule($rules, 'last');
        $this->rule($rules);
        $this->generateSql();

        return self::$conn->fetchAssoc($this->sql . ' LIMIT 1');
    }

    /**
     * 得到详情
     *
     * @param string $id  主键ID
     * @param $rules 规则
     * @return array|mixed
     */
    public function getInfo($id,  $rules = array())
    {
        $this->setSupplementWhere($this->get('unicorn.sql')->sqlMosaic(" AND sql_pre.{$this->tel_sql_array['primary_key']} = ? ", array($id)));

        return $this->getAssoc($rules);
    }

    /**
     * 是否存在
     *
     * @param string $search_val  匹配值
     * @param string $search_field 匹配字段  默认为主键字段
     * @param $rules  规则
     * @param int $exclude_primary_key_id  排除匹配的主键id  默认值null [null的时候不参与查询]
     * @return bool|mixed
     */
    public function is_exist($search_val,  $search_field = '', $rules = [] ,$exclude_primary_key_id = null)
    {
        if(empty($search_field)){
            $search_field =  'sql_pre.' . $this->tel_sql_array['primary_key'];
        }

        $this->setSupplementWhere(" AND {$search_field} = '{$search_val}' ");

        if(!empty($exclude_primary_key_id)){
            $this->setSupplementWhere( " AND sql_pre.{$this->tel_sql_array['primary_key']} <> '$exclude_primary_key_id' ");
        }

        return $this->getColumn('*', $rules, 0);
    }

    ########### TODO 查询基础方法

    /**
     * 得到一个值 [数据查询-基础方法]
     *
     * @param string $column   查询字段
     * @param  $rules   规则
     * @param bool $def     默认值
     * @return bool|mixed
     */
    public function getColumn($column, $rules = array(), $def = false)
    {
        $this->setFunctionRule($rules, 'column');
        $this->rule($rules);
        $this->sql_array['select'] = $column;
        $this->generateSql();

        $val = self::$conn->fetchColumn($this->sql . ' LIMIT 1');

        return $val === false ? $def : $val;
    }

    /**
     * 得到一条数据 [数据查询-基础方法]
     *
     * @param $rules
     * @return array
     */
    public function getAssoc($rules = array())
    {
        $this->setFunctionRule($rules, 'info');
        $this->rule($rules);
        $this->generateSql();

        return self::$conn->fetchAssoc($this->sql . ' LIMIT 1');
    }

    /**
     * 得到列表 [数据查询-基础方法]
     *
     * @param $rules 规则
     * @param string | array $OM 输出模式
     * @return array
     */
    public function getList($rules = array(), $OM = array(self::OM_LIMIT_LIST, self::OM_COUNT))
    {
        $this->setFunctionRule($rules, 'list');

        $this->rule($rules);
        $this->generateSql();

        if(!is_array($OM)){
            $OM = array($OM);
        }

        $return_array = [];

        foreach ($OM as $value){
            if($value == self::OM_ALL || $value == self::OM_COUNT){
                $return_array['count'] = $this->get('unicorn.sql')->getCount($this->sql);
            }
            if($value == self::OM_ALL || $value == self::OM_ALL_LIST){
                $return_array['info'] =  self::$conn->fetchAll($this->sql);
            }
            if($value == self::OM_ALL || $value == self::OM_LIMIT_LIST){
                $return_array['info'] =  self::$conn->fetchAll($this->get('unicorn.sql')->sqlMosaicLimit($this->sql));
            }
            if($value == self::OM_ALL || $value == self::OM_SQL){
                $return_array['sql'] = $this->sql;
            }
            if($value == self::OM_ALL || $value == self::OM_SQL_ARRAY){
                $return_array['sql_array'] = $this->sql_array;
            }
        }

        return $return_array;
    }

    #############TODO delete

    /**
     * 重写删除方法 为delete()的嵌套重写再重写方法
     *
     * @param $id
     */
    public function rewritesDelete($id){}

    /**
     * 重写删除方法 为delete()的嵌套重写方法
     *
     * @param $id
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function rewriteDelete($id)
    {
        $this->rewritesDelete($id);

        return self::$conn->executeUpdate('DELETE FROM ' . $this->table_name . ' WHERE ' . $this->tel_sql_array['primary_key'] . ' in ('. $id . ')');
    }


    #############TODO 规则

    public function rule($rules = array())
    {
        $this->paramRulesToArray($rules);

        $this->sql_array = $this->tel_sql_array;

        $this->rewriteRule($rules);

        if($rules['function'] !== 'column'){
            if(array_key_exists(self::R_SELECT , $rules)){
                $this->sql_array['select'] = $rules[self::R_SELECT] . $this->sql_array['select'];
            }
        }else{
            $this->sql_array['select'] = '';
        }

        if ($rules['function'] != 'last') {
            if (isset($rules[self::R_ORDER_BY])) {
                if (is_array($rules[self::R_ORDER_BY])) {
                    foreach ($rules[self::R_ORDER_BY] as $value) {
                        $this->sql_array['order_by'] .= ' ' . $value . ' ,';
                    }
                } else {
                    $this->sql_array['order_by'] .= ' ' . $rules[self::R_ORDER_BY] . ' ,';
                }
            }

            $this->sql_array['order_by'] .= $this->sql_array['pre'] . $this->sql_array['final_order_by'];

        }else{
            $this->sql_array['order_by'] = ' '. $this->sql_array['pre'] . '.' . $this->tel_sql_array['primary_key'] . ' DESC';
        }

        if (isset($rules[self::R_WHERE])) {
            if (is_array($rules[self::R_WHERE])) {
                foreach ($rules[self::R_WHERE] as $value) {
                    $this->sql_array['where'] .= ' ' . $value . ' ';
                }
            } else {
                $this->sql_array['where'] .= ' ' . $rules[self::R_WHERE] . ' ';
            }
        }

        if (isset($rules[self::R_JOIN])) {
            if (is_array($rules[self::R_JOIN])) {
                foreach ($rules[self::R_JOIN] as $value) {
                    $this->sql_array['join'] .= ' ' . $value . ' ';
                }
            } else {
                $this->sql_array['join'] .= ' ' . $rules[self::R_JOIN] . ' ';
            }
        }

        $this->sql_array['where'] .= $this->supplement_where;
    }

    /**
     * 重写规则
     *
     * @param array $rules
     */
    protected function rewriteRule(&$rules)
    {
        $this->sql_array['from'] = $this->table_name . ' as '. $this->sql_array['pre'];
        if(!array_key_exists('select' , $rules) && $rules['function'] !== 'column') {
            $this->sql_array['select'] = $this->get('unicorn.sql')->getTableColumnsSelect($this->table_name,  'sql_pre.') . $this->sql_array['select'];
        }

        $this->rewritesRule($rules);
    }

    /**
     * 规则重写在重写
     *
     * @param array $rules
     */
    protected function rewritesRule(&$rules){}


    #############TODO setInfo

    /**
     * 设置info输出
     *
     * @param $info
     * @return array
     */
    public function setInfo($info)
    {
        if(empty($info)){
            return array();
        }else{
            self::setJoinInfo($this, $info);
            $this->rewriteInfo($info);
            $this->setProjectInfo($info);
            return $info;
        }
    }

    /**
     * 重写info
     *
     * @param $info
     */
    protected function rewriteInfo(&$info){}


    public function delete($id)
    {
        return empty($id) ? false : $this->rewriteDelete($id);
    }

    ##############TODO 基础


    public function generateSql($sql = null)
    {
        if(empty($sql)) {
            $sql = 'SELECT ' . $this->sql_array['select'] . ' FROM ' . $this->sql_array['from'] . ' ' . $this->sql_array['join'];
            if(!empty($this->sql_array['where'])){
                $sql .= ' WHERE 1 ' . $this->sql_array['where'];
            }
            if(!empty($this->sql_array['order_by'])){
                $sql .= ' ORDER BY ' . $this->sql_array['order_by'];
            }

            $this->supplement_where = '';
        }

        $this->sql = str_replace('sql_pre', $this->sql_array['pre'] ,$sql);

        return $this->sql;
    }

    public function getTableName()
    {
        return $this->table_name;
    }

    public function getPre($pre = null)
    {
        return (empty($pre) ? $this->sql_array['pre'] : $pre ) . '.';
    }

    public function setSupplementWhere($supplement_where)
    {
        $this->supplement_where .= $supplement_where;
    }

    /**
     * 设置function规则
     *
     * @param $rules
     * @param $function_rule
     */
    private function setFunctionRule(&$rules, $function_rule)
    {
        $this->paramRulesToArray($rules);

        if(!array_key_exists('function', $rules)){
            $rules['function'] = $function_rule;
        }
    }

    /**
     * @param Model $model
     * @param array $rules
     */
    public static function joinInfo(Model $model, &$rules, $joinColumnName = null, $referencedColumnName = null){}

    /**
     * @param Model $model
     * @param $info
     */
    public static function setJoinInfo(Model $model , &$info){}

    /**
     * @param Model $model
     * @param array $rules
     */
    public static function getFieldName(Model $model, $rules){}

    /**
     * 处理Rules参数
     *
     * @param Rules|null|array $rules
     */
    public function paramRulesToArray(&$rules)
    {
        if(!is_array($rules)) {
            if ($rules instanceof Rules) {
                $rules = $rules->toArray();
            } else {
                $rules = array();
            }
        }
    }



}