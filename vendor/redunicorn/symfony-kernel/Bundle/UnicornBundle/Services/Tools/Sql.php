<?php

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

use RedUnicorn\SymfonyKernel\Unicorn;
use Symfony\Component\Security\Acl\Exception\Exception;

class Sql extends Unicorn
{
    /**
     * 得到表中全部字端select部分的查询语句
     *
     * @param $table_name
     * @param string $pre
     * @return string
     */
    public function getTableColumnsSelect($table_name, $pre = '')
    {
        return $pre . self::$conn->fetchColumn("SELECT GROUP_CONCAT(COLUMN_NAME SEPARATOR ', {$pre}') FROM INFORMATION_SCHEMA.Columns WHERE table_name = ? and table_schema = ?", array($table_name, self::$conn->getDatabase()));
    }

    /**
     * 通过查询列表的sql语句得到查询总数的sql语句
     *
     * @param $sql_list
     * @return string
     */
    public function getCountSql($sql_list)
    {
        //得到有效的FROM
        $brackets_array = $this->getStrBracketsPositionArray($sql_list);
        $from_array = $this->getStrAppearAllPositionArray($sql_list, 'from');
        $from_tag = $this->getFirstNotArrayBetweenitem($brackets_array, $from_array);  //取出合适的FROM
        if($from_tag !== false){
            //判断是否为子查询--找到from—tag最近的一个（）组合
            $tag = array();
            foreach ($brackets_array as $index => $value){
                if($index == 0){
                    if($from_tag < $value[0]){
                        //返回
                        $tag = $value;
                        break;
                    }
                }else{
                    if($brackets_array[$index-1][1] < $from_tag && $value[0] > $from_tag){
                        //返回
                        $tag = $value;
                        break;
                    }
                }
            }

            //如果找到标记组合，并且和FROM 标记之间由空格组成 则可判断存在子查询
            if(!empty($tag) &&  str_replace(' ','',substr($sql_list, $from_tag + 4, $tag[0] - $from_tag - 4)) == ''){
                $sql_list = substr($sql_list, $tag[0] + 1, $tag[1] - $tag[0] -1 );
                return $this->getCountSql($sql_list);
            }

            $order_by_tag = strripos($sql_list, 'order by');
            if($order_by_tag === false){
                $order_by_tag = strripos($sql_list, 'limit');
                if($order_by_tag === false) {
                    $order_by_tag = strlen($sql_list);
                }
            }
            if($order_by_tag < $from_tag){
                $order_by_tag = strlen($sql_list);
            }
            $group_tag = strripos($sql_list, 'group by');
            if($group_tag !== false){
                $sql = "SELECT count(*) as total_record_num FROM ({$sql_list}) AS count_table";
            }else{
                $sql = 'SELECT COUNT(*) ' . substr($sql_list, $from_tag, $order_by_tag - $from_tag);
            }
            return $sql;
        }else{
            throw new Exception('SQL错误:'.$sql_list);
        }
    }

    /**
     * 查询总数
     *
     * @param $sql
     * @return mixed
     */
    public function getCount($sql)
    {
        return self::$conn->fetchColumn($this->getCountSql($sql));
    }


    /**
     * sql参数拼接
     *
     * @param $sql
     * @param array $params
     * @return string
     */
    public function sqlMosaic($sql, $params = array())
    {
        $sql_array = str_split($sql);

        $i = 0;
        foreach ($sql_array as $index => $value)
        {
            if($i < count($params)){
                if($value == '?'){
                    $sql_array[$index] = "'{$params[$i]}'";
                    $i++;
                }
            }
        }

        $sql = '';
        foreach ($sql_array as $value){
            $sql .= $value;
        }

        return $sql;
    }

    /**
     *  sql拼接分页
     *
     * @param $sql
     * @return string
     */
    public function sqlMosaicLimit($sql)
    {
        $request = $this->get('unicorn.request');
        $page = $request->get('page');
        $rows = $request->get('rows');
        $pages = ($page - 1) * $rows;

        $request->query->set('rows', $rows);
        $request->query->set('pasge',$rows);

        return $sql . " LIMIT $pages, $rows";
    }

    /**
     *  通过查询总数的sql语句得到查询数据集的sql语句
     *
     * @param $sql
     * @param $select_str
     * @param $order_by
     * @return string
     */
    public function sqlList($sql, $select_str, $order_by)
    {
        return str_replace("COUNT(*)", $select_str, $sql). $order_by;
    }

    /**
     * sql_info
     *
     * @param $string
     * @return mixed|string
     */
    public function sql_in($string)
    {
        $string = trim($string, ',');
        $string = str_replace('"','', $string);
        $string = str_replace("'",'', $string);

        if(empty($string)){
            return '';
        }

        $string = '"' . $string;
        $string = str_replace(",", '","', $string);
        $string  .= '"';

        return $string;
    }

    /**
     * 实现simple_array的in查询策略
     *
     * @param $value  1,2
     * @param $column
     * @return string
     */
    public function sql_simple_array_in($value , $column)
    {
        $vs = explode(',', $value);
        $sql_array = [];

        foreach ($vs as $value) {
            $sql_array[] = " CONCAT(',', $column, ',')  LIKE '%$value%' ";
        }

        $sql = '(' . implode(' OR ', $sql_array) . ')';

        return $sql;
    }

    /**
     * 过滤
     *
     * @param $param
     * @return mixed
     */
    public function filter($param)
    {
        return $param;
    }

    /**
     * 得到字符串中括号的数组
     *
     * @param $str
     * @return array
     */
    private function getStrBracketsPositionArray($str)
    {
        $str_array = str_split($str);

        //得到括号的数组
        $brackets_array = [];
        $i = 0;
        $tag_array = array();
        foreach ($str_array as $index => $value){
            if($value == '('){
                $brackets_array[$i][0] = $index;
                array_unshift($tag_array, $i);
                $i++;
            }
            if($value == ')'){
                $brackets_array[$tag_array[0]][1] = $index;
                array_shift($tag_array);
            }
        }

        return $brackets_array;
    }

    /**
     * 得到一个字符串在另外一个字符串中的全部位置
     *
     * @param $str
     * @param $lookup_str
     * @return array
     */
    private function getStrAppearAllPositionArray($str, $lookup_str)
    {
        $str = strtolower($str);

        $count = substr_count($str,  $lookup_str);

        $arr = array();
        $j = 0;
        for($i = 0; $i < $count; $i++){
            $j = strpos($str, $lookup_str, $j);
            $arr[] = $j;
            $j = $j+1;
        }

        return $arr;
    }

    /**
     * 查询子数据成员不再主数据成员两者之间， 返回第一个满足情况的内容
     *
     * @param $main_array
     * @param $lookup_array
     * @return int
     */
    private function getFirstNotArrayBetweenitem($main_array, $lookup_array)
    {
        $brackets_array_count = count($main_array);

        foreach ($lookup_array as $index => $value){
            $j = 0;
            for ($i = 0; $i < $brackets_array_count; $i++){
                if($value > $main_array[$i][0] && $value < $main_array[$i][1]){
                    $j = 1;
                    break;
                }
            }
            if($j == 0){
                return $value;
            }
        }

        return false;
    }
}