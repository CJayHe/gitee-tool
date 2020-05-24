<?php

/**
 * 文件处理
 *
 * 维护： Jay
 * 方向： 使项目物理资源完全控制
 * 最后更新日期： 2017-12-05
 * 注意：
 *      1>全部的上传资源都保存在update目录下
 *      2>对上传资源的名称进行统一的规定和支持
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Files
{
    /**
     * @var ContainerInterface
     */
    private $container = null;

    /**
     * 初始化
     *
     * Cookies constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * 获取数据源
     *
     * @param $file_path
     * @return mixed
     */
    public function readToJson($file_path)
    {
        $file = fopen($file_path, 'r');
        $data = fread($file, filesize($file_path));
        $data = json_decode($data, true);

        fclose($file);

        return $data;
    }

    /**
     * 得到文件网络访问绝对路径
     *
     * @param $file_path
     * @param $is_force_host 是否强制拼接host
     * @return string
     */
    public function getFilePath($file_path, $is_force_host = false)
    {
        if(empty($file_path)){
            return '';
        }

        if($this->isOssFile($file_path)){
            return $this->getOssHost() . '/' . $file_path;
        }

        if($this->isFileLocal($file_path) || $is_force_host) {
            return $this->container->get('unicorn.request')->getSchemeAndHttpHost() . $this->container->get('request')->getBasePath() . '/' . $file_path;
        }

        return $file_path;
    }

    /**
     * 判断文件是否属于本系统上传
     *
     * @param $file_path
     * @return bool
     */
    public function isFileLocal($file_path)
    {
        if(strpos($file_path, $this->getUploadSaveRootDirName()) !== false) {
            return true;
        }

        return false;
    }


    /**
     * 判断文件是否为oss文件
     *
     * @param $file_path
     * @return bool
     */
    public function isOssFile($file_path)
    {
        if(strpos($file_path, $this->getOssUpdateSaveRootDirName()) !== false) {
            return true;
        }

        return false;
    }

    /**
     * 得到oss上传根目录名称
     *
     * @return mixed|string
     */
    public function getOssUpdateSaveRootDirName()
    {
        if($this->container->hasParameter('oss_upload_dir')){
            return $this->container->getParameter('oss_upload_dir');
        }else{
            return 'oss-upload';
        }
    }

    /**
     * 得到oss域名
     *
     * @return mixed|string
     */
    public function getOssHost()
    {
        if($this->container->hasParameter('oss_host')){
            return $this->container->getParameter('oss_host');
        }else{
            return 'oss_host';
        }
    }

    /**
     * 得到上传保存根目录名称
     *
     * @return mixed|string
     */
    public function getUploadSaveRootDirName()
    {
        if($this->container->hasParameter('dir_upload')){
            return $this->container->getParameter('dir_upload');
        }else{
            return 'upload';
        }
    }

    /**
     * 写日志
     *
     * @param array|string $content  //可以为一维度数组
     * @param string $file_name
     */
    public function writeLog($content, $file_name = 'prod.log')
    {
        if(!is_array($content)){
            $content = array($content);
        }

        foreach ($content as $index => $value){
            $content[$index] = sprintf('[%s]****%s****'. "\n", date('Y-m-d H:i:s'), $value);
        }

        file_put_contents($this->container->get('kernel')->getRootDir(). DIRECTORY_SEPARATOR .'logs'. DIRECTORY_SEPARATOR .$file_name , $content , FILE_APPEND);
    }

    /**
     * 读取日志
     *
     * @param string $file_name 日志名称
     * @param integer $rows 行数
     */
    public function readLog($file_name = 'prod.log', $rows = 20)
    {
        $path = $this->container->get('kernel')->getRootDir(). DIRECTORY_SEPARATOR .'logs'. DIRECTORY_SEPARATOR. $file_name;
        if(!file_exists($path)){
            throw new NotFoundHttpException();
        }
        $return = $this->readFile($path, $rows);
        for ($i = count($return) - 1; $i > -1; $i --){
            echo $return[$i];
            echo '<br>';
        }
    }

    /**
     * 读取文件最后几行
     *
     * @param $file
     * @param $lines
     * @return array
     */
    public function readFile($file, $lines)
    {
        //global $fsize;
        $handle = fopen($file, "r");
        $linecounter = $lines;
        $pos = -2;
        $beginning = false;
        $text = array();
        while ($linecounter > 0) {
            $t = " ";
            while ($t != "\n") {
                if(fseek($handle, $pos, SEEK_END) == -1) {
                    $beginning = true;
                    break;
                }
                $t = fgetc($handle);
                $pos --;
            }
            $linecounter --;
            if ($beginning) {
                rewind($handle);
            }
            $text[$lines-$linecounter-1] = fgets($handle);
            if ($beginning) break;
        }
        fclose ($handle);

        return array_reverse($text);
    }

    /**
     * 根地址
     *
     * @return string
     */
    public static function rootdir()
    {
        return dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))));
    }


}
