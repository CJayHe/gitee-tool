<?php

namespace BaseBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SystemController extends BaseController
{
    public function __construct(ContainerInterface $container = null)
    {
        //使用时取消注释，使用完添加注释
        throw new NotFoundHttpException();

        ini_set("max_execution_time", "0");
        ini_set('memory_limit', '-1');
        error_reporting(E_ERROR | E_PARSE);

        ob_end_clean();
        ob_implicit_flush(1);

        parent::__construct($container);
    }
}