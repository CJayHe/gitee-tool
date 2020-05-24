<?php
/**
 * 第三方接口
 */

namespace RedUnicorn\SymfonyKernel\Appoint\ThirdParty;

interface ThirdPartyInterface
{
    /**
     * 返回值重写
     *
     * @param $return
     * @return array
     */
    public function returnRewrite($return);
}