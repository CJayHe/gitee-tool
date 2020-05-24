<?php

/**
 * 报错信息记录
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

use RedUnicorn\SymfonyKernel\Unicorn;

class Error extends Unicorn
{
    /**
     * 错误信息储存
     *
     * @var array
     */
    private static $errors = [];


    /**
     * 返回第一条error信息
     *
     * @return null
     */
    public function getError()
    {
        return reset(self::$errors);
    }

    /**
     * 返回第一条 error 的 message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $error = $this->getError();

        return isset($error['message']) ? $error['message'] : '';
    }


    /**
     * 设置一条error信息
     *
     * @param $message
     * @param null $invalid_value  错误值
     * @param string $property_path 属性路径
     */
    public function setError($message, $invalid_value = null, $property_path = null)
    {
        $array = [
            'message' => $message,
            'invalid_value' => $invalid_value,
            'property_path' => $property_path
        ];

        if(empty($property_path)){
            self::$errors[] = $array;
        }else{
            self::$errors[$property_path] = $array;
        }
    }

    public function validate($class)
    {
        $errors = $this->get('validator')->validate($class);
        foreach ($errors as $error){
            $this->setError(
                $error->getMessage(),
                $error->getInvalidValue(),
                $error->getPropertyPath()
            );
        }

        return $this->getErrors();
    }

    /**
     * 返回所有error信息
     *
     * @return null
     */
    public function getErrors()
    {
        return self::$errors;
    }

    /**
     * 清除错误信息
     */
    public function clear()
    {
        self::$errors = [];
    }
}
