<?php

/**
 * 字符串辅助类
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;


class Strings
{
    /**
     * 验证码类型字母
     */
    const RANDOM_CAPTCHA = 1;

    /**
     * 验证码类型字母
     */
    const RANDOM_NUMBER = 2;

    /**
     * 加密密码
     *
     * @param $password
     * @param string $salt
     * @return string
     */
    public function encryptPassword($password, $salt = '')
    {
        return sha1(md5($password) . $salt);
    }

    /**
     * 生成盐值
     * @param int $length
     * @param bool $has_letter
     * @return string
     */
    public function generateSalt($length = 6, $has_letter = false)
    {
        $salt = '';
        if ($has_letter) {
            $intermediateSalt = md5(uniqid(rand(), true));
            $salt = substr($intermediateSalt, 0, $length);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $salt .= mt_rand(0, 9);
            }
        }
        
        return $salt;
    }
    
    /**
     * 生成订单号
     *
     * @param $user_id
     * @return string
     */
    public function generateOrderNo($user_id)
    {
        return mt_rand(10, 99)
        . sprintf('%010d', time() - 946656000)
        . sprintf('%03d', (float)microtime() * 1000)
        . sprintf('%03d', (int)$user_id % 1000);
    }

    /**
     * 生成唯一编号
     *
     * @return string
     */
    public function guid()
    {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        return
            substr($charid, 0, 8).
            substr($charid, 8, 4).
            substr($charid,12, 4).
            substr($charid,16, 4).
            substr($charid,20,12);
    }

    /**
     * 生成token值
     *
     * @param $phone
     * @return string
     */
    public function generateToken($phone)
    {
        return substr(md5($phone), 6, 16) . uniqid();
    }
    
    /**
     * 产生随机码(默认长度为4)【数字】
     *
     * @param int $length
     * @return string  随机码
     */
    public function generateNumberRandom($length = 4)
    {
        return $this->generateRandom(self::RANDOM_NUMBER, $length);
    }
    
    /**
     * 产生随机码(默认长度为4[字母]
     *
     * @param int $length
     * @return string  随机码
     */
    public function generateCaptchaRandom($length)
    {
        return $this->generateRandom(self::RANDOM_CAPTCHA, $length);
    }

    /**
     * 产生随机码(默认长度为4)[可选]
     *
     * @param $type  RANDOM_CAPTCHA|RANDOM_NUMBER
     * @param $length
     * @return string
     */
    public function generateRandom($type, $length)
    {
        $chars = '';
        if ($type === self::RANDOM_CAPTCHA) {
            $chars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHJKLMNPQEST0123456789';
        } else if ($type === self::RANDOM_NUMBER) {
            $chars = '0123456789'; 
        }
        
        $randomStr = '';
        $len = strlen($chars);
        for ($i=0; $i < $length; $i++){
            $randomStr .= $chars[rand(0,$len-1)];
        }
        
        return $randomStr;
    }
    
    /**
     * 处理NULL类型数据
     *
     * @param $value
     * @return string
     */
    public function filterNull($value)
    {
        return ($value === NULL || $value ===  false) ? '' : $value;
    }

    /**
     * 隐藏字符串中间几位
     *
     * @param $str
     * @param $start
     * @param $length
     * @return mixed|string
     */
    public function hideString($str, $start, $length)
    {
        if (!$str) {
            return '';
        }
        
        $replaceString = '';
        for($i = 0; $i < $length; $i++) {
            $replaceString .= '*';
        }
        
        return substr_replace($str, $replaceString, $start, $length);
    }
    
    /**
     * 二维数组拼接成字符串
     *
     * @param array $array
     * @param string $glue
     * @return string
     */
    public function array2String(array $array, $glue = '&')
    {
        $string = '';
        foreach($array as $key => $value) {
            $string .=  $glue . $key . '=' . $value;
        }
        
        return trim($string, $glue);
    }
    
    /**
     * 得到汉字首字母
     *
     * @param $str
     * @return null|string
     */
    public function getFirstLetter($str)
    {
        if (empty($str)) {
            return '';
        }
        if($str == '讴'){
            return 'O';
        } 
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) return strtoupper($str{0});
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) return 'A';
        if ($asc >= -20283 && $asc <= -19776) return 'B';
        if ($asc >= -19775 && $asc <= -19219) return 'C';
        if ($asc >= -19218 && $asc <= -18711) return 'D';
        if ($asc >= -18710 && $asc <= -18527) return 'E';
        if ($asc >= -18526 && $asc <= -18240) return 'F';
        if ($asc >= -18239 && $asc <= -17923) return 'G';
        if ($asc >= -17922 && $asc <= -17418) return 'H';
        if ($asc >= -17417 && $asc <= -16475) return 'J';
        if ($asc >= -16474 && $asc <= -16213) return 'K';
        if ($asc >= -16212 && $asc <= -15641) return 'L';
        if ($asc >= -15640 && $asc <= -15166) return 'M';
        if ($asc >= -15165 && $asc <= -14923) return 'N';
        if ($asc >= -14922 && $asc <= -14915) return 'O';
        if ($asc >= -14914 && $asc <= -14631) return 'P';
        if ($asc >= -14630 && $asc <= -14150) return 'Q';
        if ($asc >= -14149 && $asc <= -14091) return 'R';
        if ($asc >= -14090 && $asc <= -13319) return 'S';
        if ($asc >= -13318 && $asc <= -12839) return 'T';
        if ($asc >= -12838 && $asc <= -12557) return 'W';
        if ($asc >= -12556 && $asc <= -11848) return 'X';
        if ($asc >= -11847 && $asc <= -11056) return 'Y';
        if ($asc >= -11055 && $asc <= -10247) return 'Z';
        
        return null;
    }

    /**
     * 数字转字母
     * 导入excel时 第一栏是键值0->A 1->B 2->C 25->Z 26->AA
     *
     * @param int $pColumnIndex
     * @return mixed
     */
    public static function stringFromColumnIndex($pColumnIndex = 0)
    {
        //  Using a lookup cache adds a slight memory overhead, but boosts speed
        //  caching using a static within the method is faster than a class static,
        //      though it's additional memory overhead
        static $_indexCache = array();

        if (!isset($_indexCache[$pColumnIndex])) {
            // Determine column string
            if ($pColumnIndex < 26) {
                $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
            } elseif ($pColumnIndex < 702) {
                $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)) . chr(65 + $pColumnIndex % 26);
            } else {
                $_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676)) . chr(65 + ((($pColumnIndex - 26) % 676) / 26)) . chr(65 + $pColumnIndex % 26);
            }
        }

        return $_indexCache[$pColumnIndex];
    }
}
