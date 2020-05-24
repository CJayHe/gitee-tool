<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/7
 */

namespace RedUnicorn\SymfonyKernel\ClassGeneration;


class Argument extends \ClassGeneration\Argument
{
    private $is_status = false;

    public function setIsStatus($is_status)
    {
        return $this->is_status = $is_status;
    }

    public function getIsStatus()
    {
        return $this->is_status;
    }

    public function toString()
    {
        $type = '';
        if ($this->hasType()) {
            $type = $this->getType() . ' ';
        }

        $status = '';
        if($this->getIsStatus()){
            $status = '&';
        }

        $value = '';
        if ($this->isOptional()) {
            $value = ' = ' . var_export($this->getValue(), true);
        }
        $argument = trim(
            $type
            . $status
            . $this->getNameFormatted()
            . $value
        );

        return $argument;
    }
}