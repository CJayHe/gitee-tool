<?php
namespace RedUnicorn\SymfonyKernel\ClassGeneration;

class PhpClass extends \ClassGeneration\PhpClass
{
    public function toString()
    {
        $extends = '';
        if ($this->getExtends()) {
            $extends = ' extends ' . $this->getExtends();
        }

        $string = '<?php' . PHP_EOL
            . $this->getNamespace()->toString() . PHP_EOL
            . $this->getUseCollection()->toString()
            . $this->getDocBlock()->setTabulation($this->getTabulation())->toString()
            . $this->toStringType()
            . $this->getName()
            . $extends
            . $this->getInterfaceCollection()->toString()
            . PHP_EOL
            . '{'
            . PHP_EOL
            . $this->getCompositionCollection()->toString()
            . $this->getConstantCollection()->toString()
            . $this->getPropertyCollection()->toString()
            . $this->getMethodCollection()->toString()
            . '}' . PHP_EOL;

        return $string;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}