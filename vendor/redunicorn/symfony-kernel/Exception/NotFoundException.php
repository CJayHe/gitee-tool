<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 2018/9/13
 * Time: 上午11:27
 */

namespace RedUnicorn\SymfonyKernel\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class NotFoundException extends HttpException implements ApiExceptionInterface
{
    public function __construct($message = null, \Exception $previous = null, array $headers = array())
    {
        parent::__construct(Response::HTTP_NOT_FOUND, $message, $previous, $headers, Response::HTTP_NOT_FOUND);
    }
}