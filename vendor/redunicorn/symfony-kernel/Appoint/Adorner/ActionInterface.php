<?php
/**
 * Action 装饰器
 * User: Jay
 * Date: 2018/3/23
 */

namespace RedUnicorn\SymfonyKernel\Appoint\Adorner;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

interface ActionInterface
{
    public function onKernelController(FilterControllerEvent $event);
}