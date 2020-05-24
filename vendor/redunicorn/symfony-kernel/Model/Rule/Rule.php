<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/22
 */
namespace RedUnicorn\SymfonyKernel\Model\Rule;

use RedUnicorn\SymfonyKernel\Model\Model;
use RedUnicorn\SymfonyKernel\Model\Rule\Joint\ModelDefaultArrayRuleJoint;
use RedUnicorn\SymfonyKernel\Model\Rule\Joint\StringRuleJoint;

class Rule
{

    /**
     * 碰撞规则 - 取代
     */
    const REPLACE = 'replace';

    /**
     * 碰撞规则 - 放弃
     */
    const FORGO = 'forgo';

    /**
     * 碰撞规则 - 联合
     */
    const JOINT = 'joint';

    /**
     * 撞规则-联合-次序规则-正序  （上一级排在前面）
     */
    const ASC = 'asc';

    /**
     * 撞规则-联合-次序规则-倒序   （当前级排在前面）
     */
    const DESC = 'desc';

    /**
     * 规则名称
     *
     * @var
     */
    private $name;

    /**
     * 规则内容
     *
     * @var
     */
    private $value;

    /**
     * 碰撞规则
     *
     * @var string
     */
    private $collision;

    /**
     * @var JointInterface
     */
    private $jointClass;

    /**
     * @var 碰撞规则-联合-次序规则
     */
    private $jointSort;

    /**
     * Rule constructor.
     * @param $name
     * @param $value
     * @param null $collision
     * @param null | JointInterface $jointClass
     * @param $jointSort
     */
    public function __construct($name, $value, $collision = null, $jointClass = null, $jointSort = null)
    {
        if($collision == null) {
            if(in_array($name, array(Model::R_ORDER_BY, Model::R_WHERE, Model::R_JOIN))) {
                $collision = self::JOINT;
                $jointClass = new ModelDefaultArrayRuleJoint();
            }else{
                $collision = self::REPLACE;
            }
        }

        if($collision == self::JOINT && empty($jointClass)){
            $jointClass = new StringRuleJoint();
        }

        if($collision == self::JOINT && empty($jointSort)){
            $jointSort == self::ASC;
        }

        $this->name = $name;
        $this->value = $value;
        $this->collision = $collision;
        $this->jointClass = $jointClass;
        $this->jointSort = $jointSort;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getCollision()
    {
        return $this->collision;
    }

    /**
     * @return JointInterface
     */
    public function getJointClass()
    {
        return $this->jointClass;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getJointSort()
    {
        return $this->jointSort;
    }
}