<?php

/**
 * 系统辅助类
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;


class Systems
{
    /**
     * 是否为windows
     *
     * @return bool
     */
    public function isWindows()
    {
        return strncasecmp(PHP_OS, 'WIN', 3) == 0;
    }

    /**
     * 是否为mac
     *
     * @return bool
     */
    public function isMac()
    {
        return strncasecmp(PHP_OS, 'Darwin', 6) == 0;
    }

    /**
     * 判断是否微信浏览器
     *
     * @return boolean
     */
    public function isWeixin()
    {
        return ($this->getOS() === 'weixin') ? true : false;
    }

    /**
     * 验证ip是否国外【非中国】
     *
     * @param $ip
     * @return bool
     */
    public function isAbroad($ip)
    {
        $location = file_get_contents("http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip=" . $ip);
        $location = json_decode($location, true);

        if ($location && is_array($location) && array_key_exists('country', $location)) {
            if ($location['country'] != '中国') {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取系统
     *
     * @return string
     */
    public function getOS()
    {
        $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (strpos($agent, 'micromessenger')) {
            $platform = 'weixin';
        } elseif (strpos($agent, 'windows nt')) {
            $platform = 'windows';
        } elseif (strpos($agent, 'macintosh')) {
            $platform = 'mac';
        } elseif (strpos($agent, 'ipod')) {
            $platform = 'ipod';
        } elseif (strpos($agent, 'ipad')) {
            $platform = 'ipad';
        } elseif (strpos($agent, 'iphone')) {
            $platform = 'iphone';
        } elseif (strpos($agent, 'android')) {
            $platform = 'android';
        } elseif (strpos($agent, 'unix')) {
            $platform = 'unix';
        } elseif (strpos($agent, 'linux')) {
            $platform = 'linux';
        } else {
            $platform = 'other';
        }

        return $platform;
    }
}
