<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/6/7
 */

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Request;


use RedUnicorn\SymfonyKernel\Appoint\Adorner\RequestAfterInterface;
use RedUnicorn\SymfonyKernel\Unicorn;

class SqlFilter extends Unicorn implements RequestAfterInterface
{
    public function register()
    {
        // TODO: Implement register() method.
        return [
            ['name' => 'filter_sql', 'description' => '过滤sql注入']
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
        if(!in_array('filter_sql' , $unfasten)){
            $value = $this->get('unicorn.sql')->filter($value);
        }

        return $value;
    }

}