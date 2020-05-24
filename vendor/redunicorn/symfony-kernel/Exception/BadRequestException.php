<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 2018/9/13
 * Time: 上午11:28
 */

namespace RedUnicorn\SymfonyKernel\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BadRequestException extends HttpException implements ApiExceptionInterface
{
    public function __construct($message = null, \Exception $previous = null, array $headers = array())
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, Response::HTTP_BAD_REQUEST);
    }
}
