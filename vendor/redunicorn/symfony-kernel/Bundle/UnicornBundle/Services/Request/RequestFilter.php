<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/4/26
 */

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Request;


use RedUnicorn\SymfonyKernel\Appoint\Adorner\RequestAfterInterface;
use RedUnicorn\SymfonyKernel\Unicorn;

class RequestFilter extends Unicorn implements RequestAfterInterface
{
    public function register()
    {
        // TODO: Implement register() method.
        return  [
            ['name' => 'filter_page' , 'description' => '过滤分页'],
            ['name' => 'filter_phiz' , 'description' => '过滤表情']
        ];
    }

    public function dir()
    {
        // TODO: Implement dir() method.
        return __FILE__;
    }

    public function after($key, $value, $unfasten = [])
    {
        // TODO: Implement after() method.
        if(!in_array('filter_page' , $unfasten) && in_array($key , ['page', 'rows'])){
            if (!(int)$value) {
                if($key == 'page'){
                    $value = 1;
                }else{
                    $value = $this->container->hasParameter('rows') ? $this->container->getParameter('rows') : 15;
                }
            }elseif($value < 1){
                if($key == 'page'){
                    $value = 1;
                }else{
                    $value = $this->container->hasParameter('rows') ? $this->container->getParameter('rows') : 15;
                }
            }
        }

        if (!in_array('filter_phiz', $unfasten)) {
            $this->get('unicorn.filters')->encodeContentWithEmoticon($value, true);
        }

        return $value;
    }
}