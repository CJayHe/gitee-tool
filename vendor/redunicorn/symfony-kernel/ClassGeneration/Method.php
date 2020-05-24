<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/6
 */

namespace RedUnicorn\SymfonyKernel\ClassGeneration;


use ClassGeneration\ArgumentInterface;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Command\GenerateModel\ModelInfo;

class Method extends \ClassGeneration\Method
{
    public function setName($name)
    {
        $this->name = $name;
    }

    public function setCode($code = '')
    {
       $this->code .= $code . PHP_EOL;
    }

    public function addArgument(ArgumentInterface $argument)
    {
        $this->getArgumentCollection()->add($argument);
    }

    public function getCode()
    {
        return rtrim(parent::getCode());
    }
}