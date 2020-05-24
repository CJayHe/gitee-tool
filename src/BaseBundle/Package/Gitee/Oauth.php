<?php
/**
 * PhpStorm.
 *
 * 接口文档地址： https://gitee.com/api/v5/oauth_doc
 *
 * User: Jay
 * Date: 2018/11/20
 */

namespace Gitee;

class Oauth
{
    const SESSION_NAME = 'gitee_assess_info';

    public static function getAccessToken()
    {
        if (!session_id()) session_start();

        if(array_key_exists(self::SESSION_NAME, $_SESSION)){
            if($_SESSION[self::SESSION_NAME]['expired_time']  > (time() + 10)) {
                return $_SESSION[self::SESSION_NAME]['assess_token'];
            }
        }

        $res = json_decode(self::http_post('https://gitee.com/oauth/token', array(
            'username' => USERNAME,
            'password' => PASSWORD,
            'client_id' => CLIENT_ID,
            'client_secret' => CLIENT_SECRET,
            'grant_type' => 'password',
            'scope' => ' projects enterprises user_info',
        )), true);


        if(empty($res)){
            die('请求失败');
        }

        if(array_key_exists('error', $res)){
            die($res['error_description']);
        }

        $_SESSION[self::SESSION_NAME] = array(
            'assess_token' => $res['access_token'],
            'expired_time' => time() + $res['expires_in']
        );

        return $res['access_token'];
    }

    public static function http_post($url, $data_string)
    {
        $data_string = http_build_query($data_string);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-AjaxPro-Method:ShowList',
                'Content-Type:application/x-www-form-urlencoded ',
                'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function http_get($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);
        curl_close($ch);

        return $output;
    }

    public static function http_put($url, $data_string = array())
    {
        $data_string = http_build_query($data_string);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-AjaxPro-Method:ShowList',
                'Content-Type:application/x-www-form-urlencoded ',
                'Content-Length: ' . strlen($data_string))
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

    public static function http_delete($url, $data_string = array())
    {
        $data_string = http_build_query($data_string);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'X-AjaxPro-Method:ShowList',
                'Content-Type:application/x-www-form-urlencoded ',
                'Content-Length: ' . strlen($data_string))
        );

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }

}