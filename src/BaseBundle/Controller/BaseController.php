<?php

/**
 * 项目开发基础类
 *
 * 定义项目的全局对象和基本写法
 */

namespace BaseBundle\Controller;

use RedUnicorn\SymfonyKernel\Exception\UnicornException;
use RedUnicorn\SymfonyKernel\Model\Model;

abstract class BaseController extends UnicornController
{
    /**
     * 得到操作主体
     *
     * @param string $pre
     * @param null $subject_type  设置非空值则自定义当前主体类型
     * @param null $subject_id subject_type 不为空则生效
     * @return array
     */
    final protected function getSubject($pre = '', $subject_type = null, $subject_id = null)
    {
        if(empty($subject_type)) {
            $subject_type = $this->getParameter('subject_system');
            $subject_id = null;

            if(!empty($this->getUserId())) {
                switch ($this->getPlatform()) {
                    //TODO 重写 -> 自主识别当前主体
//                    case $this->getParameter('platform_website'):
//                        $subject_type = $this->getParameter('待配置的主体类型');
//                    break;
                    default:
                        $this->ex('来源不正确');

                }

                $subject_id = $this->getUserId();
            }


        }else {
            if(in_array($subject_type, $this->getParameter('subject_types'))){
                throw new UnicornException('操作主体溢出');
            }
        }

        return array(
            $pre . 'subject_id' => $subject_id,
            $pre . 'subject_type' => $subject_type
        );
    }

    /**
     * 得到客户端域名
     *
     * @param $platform
     * @return string
     */
    final public function getClientHost($platform)
    {
        $host = '';

        switch ($platform){
            //TODO 实现
//            case $this->getParameter('platform_website'):
//                if($this->getParameter('site_mark') == 'master'){
//                    $host = 'http://beta站域名';
//                }else{
//                    $host = 'http://主站域名';
//                }
//                break;
            default:
                $this->ex('来源不正确');
        }

        return $host;
    }

    /**
     * 设置用户标记
     *
     * @param $user_tag
     * @return mixed
     */
    final protected function setUserTag($user_tag)
    {
        switch ($this->getPlatform()){
            //TODO 实现
//            case $this->getParameter('platform_website'):
//                $this->get('unicorn.sessions')->set($this->getPlatform() . $this->getParameter('login_tag_session_name'), $user_tag);
//                break;
            default:
                $this->ex('来源不正确');
        }

        return $user_tag;
    }

    /**
     * 得到用户标记
     *
     * @param null $user_tag
     * @return null
     */
    final protected function getUserTag($user_tag = null)
    {
        switch ($this->getPlatform()){
            //TODO 实现
//            case $this->getParameter('platform_website'):
//                $this->get('unicorn.sessions')->get($this->getPlatform() . $this->getParameter('login_tag_session_name'));
//                break;
            default:
                $this->ex('来源不正确');
        }

        return $user_tag;
    }

    /**
     * 移除用户标记
     *
     * @param null $user_tag
     * @return null
     */
    final protected function removeUserTag($user_tag = null)
    {
        switch ($this->getPlatform()){
            //TODO 实现
//            case $this->getParameter('platform_website'):
//                $this->get('unicorn.sessions')->remove($this->getPlatform() . $this->getParameter('login_tag_session_name'));
//                break;
            default:
                $this->ex('来源不正确');
        }

        return $user_tag;
    }

    /**
     * 列表页面读取模式
     *
     * @param null $OM
     * @return array|int|null
     */
    public function listPageGetOM($OM = null)
    {
        if(empty($OM)){
            switch ($this->getPlatform()){
                default:
                    return Model::OM_LIMIT_LIST;
            }
        }

        return $OM;
    }

    /**
     * 验证批量操作是否合法
     *
     * @param Model $model
     * @param string simple_array $ids
     * @param array $rule
     * @return bool
     */
    public function verifyBatchOperating(Model $model, $ids, $rule)
    {
        $q_ids = $model->getIds(array_merge($rule,  array(
            Model::R_WHERE => " AND sql_pre.id in ($ids)"
        )));

        if(strlen($this->get('unicorn.sql')->sql_in($q_ids)) != strlen($ids)){
            self::$error->setError('删除项溢出');
            return false;
        }

        return true;
    }
}