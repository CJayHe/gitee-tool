<?php
/**
 * Created by PhpStorm.
 * User: terry
 * Date: 2018/9/13
 * Time: 上午11:30
 */

namespace RedUnicorn\SymfonyKernel\EventListener;


use RedUnicorn\SymfonyKernel\Exception\ApiExceptionInterface;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Responses\Responses;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ApiExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (!$event->getException() instanceof ApiExceptionInterface) {
            return;
        }

        $response = $this->buildResponseData($event->getException());
        $response->setStatusCode($event->getException()->getStatusCode());

        $event->setResponse($response);
    }

    private function buildResponseData(ApiExceptionInterface $exception)
    {
        $message = json_decode($exception->getMessage(), true);

        if(is_null($message)){
            $message = $exception->getMessage();
        }

        return  Responses::json($message, $exception->getCode());
    }
}