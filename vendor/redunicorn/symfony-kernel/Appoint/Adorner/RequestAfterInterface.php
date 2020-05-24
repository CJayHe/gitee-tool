<?php

namespace RedUnicorn\SymfonyKernel\Appoint\Adorner;

interface RequestAfterInterface
{
    public function register();

    public function dir();

    /**
     * after
     *
     * @param $key
     * @param $value
     * @param array $unfasten 限制解开
     * @return mixed
     */
    public function after($key , $value, $unfasten = []);
}