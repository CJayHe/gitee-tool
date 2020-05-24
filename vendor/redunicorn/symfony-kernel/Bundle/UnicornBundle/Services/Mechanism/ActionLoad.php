<?php
/**
 * Action之前装饰器
 *
 * User: zmit
 * Date: 7/3/17
 * Time: 1:05 AM
 */

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Mechanism;

use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Request\RequestFilter;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Request\SqlFilter;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools\Error;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use RedUnicorn\SymfonyKernel\Appoint\Adorner\ActionInterface;
use RedUnicorn\SymfonyKernel\Unicorn;

class ActionLoad extends Unicorn implements ActionInterface
{
    public function onKernelController(FilterControllerEvent $event)
    {
        self::$conn = $this->get('database_connection');
        self::$request = $this->get('unicorn.request');
        self::$error = new Error($this->container);
        self::$trans = $this->get('translator');
        self::$request->addRequestAfter(new RequestFilter($this->container));
        self::$request->addRequestAfter(new SqlFilter($this->container));
    }
}