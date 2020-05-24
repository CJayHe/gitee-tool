<?php

/**
 * 响应封装
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Responses;

use RedUnicorn\SymfonyKernel\Unicorn;
use Symfony\Component\HttpFoundation\Response;

class Responses
{
    /**
     * 返回数据格式json
     */
    const RESPONSE_JSON = 'json';

    /**
     * 返回数据格式xml
     */
    const RESPONSE_XML = 'xml';

    /**
     * 返回数据格式array
     */
    const RESPONSE_ARRAY = 'array';

    /**
     * 返回数据格式debug
     */
    const RESPONSE_DEBUG = 'debug';

    /**
     * 返回值处理
     *
     * @param string|array $message 错误信息
     * @param int $errorCode  错误标识
     * @param array $data     返回数据
     * @param string $type    返回类型
     * @return array|Response
     */
    private static function value($message, $errorCode = 0, array $data = array(), $type = self::RESPONSE_DEBUG)
    {
        if(!is_array($message)){
            $message = array('message' => $message);
        }

        $result = array(
            'errorCode' => $errorCode,
            'message' => $message
        );

        if(!empty(Unicorn::getGlobalReturn())) {
            $data['global_data'] = Unicorn::getGlobalReturn();
        }

        if(!empty($data)){
            $result['data'] = $data;
        }

        if ( $type == self::RESPONSE_JSON ) { // json 格式
            $response = new Response(json_encode($result));
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        } else if ( $type == self::RESPONSE_XML ) { // 返回xml数据

            $xml = '<?xml version="1.0" encoding="UTF-8"?>';
            $response = new Response($xml);
            $response->headers->set('Content-Type', 'xml');

            return $response;
        } else if ( $type = self::RESPONSE_ARRAY ) { // 返回数组array

            return $result;
        }
        // 未知的情况下，直接打印
        var_dump($result); exit;
    }

    /**
     * 返回json
     * 
     * @param  $message
     * @param int $errorCode
     * @param array $data
     * @return array|Response
     */
    public static function json($message, $errorCode = 1, array $data = array())
    {
        return self::value($message, $errorCode, $data, self::RESPONSE_JSON);
    }

    /**
     * 返回xml数据
     *
     * @param  $message
     * @param int $errorCode
     * @param array $data
     * @return array|Response
     */
    public static function xml($message, $errorCode = 1, array $data = array())
    {
        return self::value($message, $errorCode, $data, self::RESPONSE_XML);
    }

    /**
     * 返回数组
     *
     * @param $message
     * @param int $errorCode
     * @param array $data
     * @return array|Response
     */
    public static function arrays($message, $errorCode = 1, array $data = array())
    {
        return self::value($message, $errorCode, $data, self::RESPONSE_ARRAY);
    }

    /**
     * 测试模式/未知模式
     *
     * @param $message
     * @param int $errorCode
     * @param array $data
     * @return array|Response
     */
    public static function debug($message, $errorCode = 1, array $data = array())
    {
        return self::value($message, $errorCode, $data, self::RESPONSE_DEBUG);
    }
}
