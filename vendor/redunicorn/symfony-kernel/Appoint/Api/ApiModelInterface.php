<?php
namespace RedUnicorn\SymfonyKernel\Appoint\Api;

use RedUnicorn\SymfonyKernel\Model\Rule\Rules;

/**
 * 对model层操作需实现该类
 *
 * Interface ApiModelInterface
 */
interface ApiModelInterface
{
    /**
     * 在这个方法中定义当前类api的普通适用规则
     *
     * @param array|Rules|null $rules
     * @return array|Rules
     */
    public function getRules($rules = null);
}