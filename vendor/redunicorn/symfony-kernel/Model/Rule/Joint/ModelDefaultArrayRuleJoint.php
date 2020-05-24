<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/22
 */

namespace RedUnicorn\SymfonyKernel\Model\Rule\Joint;


use RedUnicorn\SymfonyKernel\Model\Rule\JointInterface;
use RedUnicorn\SymfonyKernel\Model\Rule\Rule;

class ModelDefaultArrayRuleJoint implements JointInterface
{
    function joint($current_rule_value, $upper_rule_value, $jointSort)
    {
        if($jointSort == Rule::DESC){
            $var = $current_rule_value;
            $current_rule_value = $upper_rule_value;
            $upper_rule_value = $var;
        }

        $value = [];

        if(is_array($upper_rule_value)){
            $value = array_merge_recursive($value, $upper_rule_value);
        }else{
            $value[] = $upper_rule_value;
        }

        if(is_array($current_rule_value)){
            $value = array_merge_recursive($value, $current_rule_value);
        }else{
            $value[] = $current_rule_value;
        }

        return $value;
    }


}