<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/7
 */

namespace BaseBundle\Controller;


use RedUnicorn\SymfonyKernel\Exception\NotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class BetaController extends BaseController
{
    /**
     * @Route("/test")
     */
    public function testAction()
    {
        throw new NotFoundException();
    }

    /**
     * @Route("/log/{file}/{rows}")
     */
    public function logAction($file, $rows = 20)
    {
        $this->get('unicorn.files')->readLog($file, $rows);

        return new Response();
    }
}