<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/22
 */
namespace RedUnicorn\SymfonyKernel\Model\Rule;

class Rules
{

    private $rules = [];

    /**
     * Rules constructor.
     * @param Rules|null|array $rules
     */
    public function __construct($rules = null)
    {
        if(!empty($rules)) {
            if(is_array($rules)){
                $this->addRules($rules);
            }else{
                $this->rules = $rules->getRules();
            }
        }
    }

    /**
     * @param Rules|null|array $rules
     */
    public function addRules($rules)
    {
        if(!empty($rules)) {

            if(is_array($rules)){
                return $this->addArrayRule($rules);
            }

            /**
             * @var Rule $rule
             */
            foreach ($rules->getRules() as $rule) {
                $this->addRule($rule);
            }
        }

        return $this;
    }

    private function addArrayRule(array $array_rule)
    {
        foreach ($array_rule as $rule => $value){
            $this->addRule(new Rule($rule, $value));
        }

        return $this;
    }

    public function addRule(Rule $rule)
    {
        if($this->issetRule($rule->getName())) {
            switch ($this->getRule($rule->getName())->getCollision()){
                case Rule::REPLACE:
                    $rule->setValue($this->getRule($rule->getName())->getValue());
                    break;
                case Rule::JOINT:
                    $rule->setValue($this->getRule($rule->getName())->getJointClass()->joint($rule->getValue(), $this->getRule($rule->getName())->getValue(), $this->getRule($rule->getName())->getJointSort()));
                    break;
            }
        }

        $this->rules[$rule->getName()] = $rule;

        return $this;
    }

    public function toArray()
    {
        $rules = [];

        /**
         * @var Rule $rule
         */
        foreach ($this->rules as $rule) {
            $rules[$rule->getName()] = $rule->getValue();
        }

        return $rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    public function issetRule($rule_name)
    {
        return array_key_exists($rule_name, $this->getRules());
    }

    /**
     * @param $rule_name
     * @return Rule
     */
    public function getRule($rule_name)
    {
        return $this->rules[$rule_name];
    }

    /**
     * @param $rule_name
     */
    public function removeRule($rule_name)
    {
        unset($this->rules[$rule_name]);
    }

}


