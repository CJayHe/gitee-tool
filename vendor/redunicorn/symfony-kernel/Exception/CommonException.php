<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 2018/9/13
 * Time: 下午12:01
 */

namespace RedUnicorn\SymfonyKernel\Exception;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommonException extends HttpException implements ApiExceptionInterface
{
    public function __construct($message = null, $code = 1, $statusCode = null, \Exception $previous = null, array $headers = array())
    {
        if (is_array($message)) {
            $message = json_encode($message);
        }
        if (null == $statusCode) {
            $statusCode = Response::HTTP_OK;
        }

        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}