<?php

/**
 * 数组处理
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

class Arrays
{

    /**
     * 得到数组中的内容 没有则返回设置的默认值
     *
     * @param array $array
     * @param $key
     * @param string $default
     * @return mixed|null|string
     */
    public function value(array $array, $key, $default = null)
    {
        // isset() is a micro-optimization - it is fast but fails for null values.
        if(isset($array[$key])) {
            return $array[$key];
        }
        // Comparing $default is also a micro-optimization.
        if($default === null || array_key_exists($key, $array)) {
            return null;
        }

        return $default;
    }

    /**
     * 数组有序化
     *
     * 用于在输出到客户端自定义数组的排序封装使用
     *
     * @param array $array
     * @param array $sort_array
     * @return array
     */
    public function ordering($array, $sort_array = [])
    {
        if(empty($sort_array)){
            $sort_array = array_keys($array);
        }

        $ordering_array = array();
        foreach ($sort_array as $key => $value) {
            $ordering_array[] = array(
                'key' => $value,
                'value' => $array[$value]
            );
        }

        return $ordering_array;
    }

    /**
     * 根据指定的一组key构建新数组
     *
     * @param $array
     * @param $keys
     * @return array
     */
    public function designationKeyConstructNewArray($array, $keys)
    {
        $new_array = [];
        foreach ($keys as $key){
            $new_array[$key] = $array[$key];
        }

        return $new_array;
    }
}
