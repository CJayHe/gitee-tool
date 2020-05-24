<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/22
 */

namespace RedUnicorn\SymfonyKernel\Model\Rule\Joint;

use RedUnicorn\SymfonyKernel\Model\Rule\JointInterface;
use RedUnicorn\SymfonyKernel\Model\Rule\Rule;

class StringRuleJoint implements JointInterface
{
    function joint($current_rule_value, $upper_rule_value, $jointSort)
    {
        if($jointSort == Rule::DESC){
            $var = $current_rule_value;
            $current_rule_value = $upper_rule_value;
            $upper_rule_value = $var;
        }

        return $upper_rule_value . ' '.$current_rule_value;
    }

}