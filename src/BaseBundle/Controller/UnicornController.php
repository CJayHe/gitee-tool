<?php
/**
 * 项目核心基础机制类
 *
 * User: Jay
 * Date: 2018/7/5
 */

namespace BaseBundle\Controller;

use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Responses\Responses;
use RedUnicorn\SymfonyKernel\Model\Model;
use RedUnicorn\SymfonyKernel\Unicorn;
use Symfony\Component\HttpFoundation\Response;

abstract class UnicornController extends Unicorn
{
    /**
     * 平台
     *
     * @var string
     */
    private static $platform;

    /**
     * 当前登录用户id
     *
     * @var string
     */
    private static $user_id;

    /**
     * 当前登录用户信息
     *
     * @var array
     */
    protected static $user_info;

    /**
     * 得到platform
     *
     * @return mixed
     */
    final protected function getPlatform()
    {
        return self::$platform;
    }

    /**
     * 设置platform
     *
     * @param mixed $platform
     */
    final protected function setPlatform($platform)
    {
        self::$platform = $platform;
    }

    /**
     * 设置user_id
     *
     * @param mixed $user_id
     */
    final protected function setUserId($user_id)
    {
        self::$user_id = $user_id;
    }

    /**
     * 得到user_id
     *
     * @return mixed
     */
    final protected function getUserId()
    {
        return self::$user_id;
    }

    /**
     * 入口方法
     *
     * @param $return_type
     * @param bool $is_login 是否强制登陆
     * @return bool
     */
    protected function inlet($return_type, $is_login = true)
    {
        if(empty(Unicorn::getReturnType())) {
            Unicorn::setReturnType($return_type);
        }

        return true;
    }

    /**
     * 设置list数据
     *
     * @param $array
     * @param Model $model
     * @return mixed
     */
    final protected function setList($array , $model)
    {
        foreach ($array as $index => $value){
            $array[$index] = $model->setInfo($value);
        }

        return $array;
    }

    /**
     * 设置项目数据[项目输出的总控制]
     *
     * @param $info
     */
    final protected function setProjectInfo(&$info){}


    /**
     * 常用输出方法
     *
     * @param $message
     * @param int $errorCode
     * @param array $data
     * @return array|bool|\Symfony\Component\HttpFoundation\Response
     */
    final protected function response($message , $errorCode = 1, $data = array())
    {
        if(Unicorn::getReturnType() == $this->getParameter('return_data') || empty(Unicorn::getReturnType())) {
            return  Responses::json($message, $errorCode, $data);
        }else{
            if(is_array($message)){
                if(isset($message['message'])){
                    $message = $message['message'];
                }else{
                    $message = '访问异常!';
                }
            }
            $this->ex($message, 'javascript:history.go(-1);', $errorCode);
        }

        return false;
    }

    /**
     * 访问异常
     *
     * @param string $message
     * @param string $url
     * @param int $errorCode
     */
    protected function ex($message = '访问异常!', $url = 'javascript:history.go(-1);', $errorCode = 1)
    {
        exit($this->render('@Base/ex.html.twig', ['message' => $message, 'url' => $url, 'errorCode' => $errorCode]));
    }

    /**
     * 网络错误服务
     *
     * @param \Exception $exception
     */
    final protected function networkError(\Exception $exception)
    {
        if(!self::$error->getError()) {
            self::$error->setError('系统繁忙，请稍后再试');
            //记录错误日志
            $this->get('unicorn.files')->writeLog(
                ' [EXCEPTION_MESSAGE] ' . $exception->getMessage() .
                ' [ EXCEPTION_FILE ] ' . $exception->getFile() .
                ' [ EXCEPTION_CODE ] ' . $exception->getCode() .
                ' [ EXCEPTION_LINE ] '. $exception->getLine() .
                ' [ ERROR ] ' . self::$error->getError()['message']
            );
        }
    }
}