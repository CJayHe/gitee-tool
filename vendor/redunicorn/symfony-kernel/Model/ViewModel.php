<?php
/**
 * 视图Model
 */

namespace RedUnicorn\SymfonyKernel\Model;

use RedUnicorn\SymfonyKernel\Model\Model;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ViewModel extends Model
{
    /**
     * @var DivideTableModel
     */
    private $divideTableModel;

    function __construct(ContainerInterface $container = null, DivideTableModel $divideTableModel)
    {
        parent::__construct($container, $divideTableModel->getViewName());
    }

    final function validate($data, $id = false)
    {
        // TODO: view 不可用
    }

    final function insert($data)
    {
        // TODO: view 不可用
    }

    final function update($id, $data)
    {
        // TODO: view 不可用
    }

    final function delete($id)
    {
        // TODO: view 不可用
    }

    public function rewriteInfo(&$info)
    {
        $this->divideTableModel->publicInfo($info);
    }

    protected function rewritesRule(&$rules)
    {
        $this->divideTableModel->publicRule($rules);
    }

}