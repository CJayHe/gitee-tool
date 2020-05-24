<?php

/**
 * 过滤处理
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

class Filters
{
    /**
     * 过滤空格/回车
     *
     * @param $str
     * @return mixed
     */
    public function space($str)
    {
        return str_replace(array(" ","　","\t","\n","\r") , array("","","","",""),$str);
    }

    /**
     *  过滤引号
     *
     * @param $str
     * @return mixed
     */
    public function quotes($str)
    {
        return str_replace(array("'",'"') , array("",""),$str);
    }

    /**
     * 处理短文本中的表情进行处理
     *
     * @param $content
     * @param bool $is_saved 是否保存到表情到数据库，是则对其进行base64_encode编码, 否则过滤掉表情
     * @return mixed
     */
    public function encodeContentWithEmoticon($content, $is_saved = false)
    {
        if( $is_saved ) {
            $content = preg_replace('~<img(.*?)>~s','',$content);
            return preg_replace_callback('/[\xf0-\xf7].{3}/', function ($r) {
                return '@E' . base64_encode($r[0]);
            }, $content);
        }

        $content = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $content
        );

        return $content;

    }

    /**
     * 恢复处理后的长文本内容
     *
     * @param $content
     * @param bool $is_restored 是否恢复表情
     * @return mixed
     */
    public function decodeContentWithEmoticon($content, $is_restored = false)
    {
        if( $is_restored ) {
            return preg_replace_callback('/@E(.{6}==)/', function ($r) {
                return base64_decode($r[1]);
            }, $content);
        }

        return $content;
    }
    
}
