<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2019/1/18
 */

namespace Gitee;


class Enterprises
{
    /**
     * 返回值处理
     *
     * @param $res
     * @return array|mixed
     */
    private function resProcess($res)
    {
        $res = json_decode($res, true);

        if(array_key_exists('message', $res)){
            return array();
        }else{
            if(is_array($res)) {
                return $res;
            }else{
                return array();
            }
        }
    }

    /**
     * 得到当前用户所属的全部企业
     *
     * @param $page
     * @param $per_page
     * @param boolean $admin  是否为管理员的企业
     * @return array|mixed
     */
    public function getEnterprises($page = 1, $per_page = 20, $admin = true)
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/user/enterprises?access_token=' . Oauth::getAccessToken() . '&page=' . $page . '&per_page=' . $per_page . '&admin=' . $admin));
    }

    /**
     * 移除企业成员
     *
     * @param $enterprise
     * @param $username
     * @return array|mixed
     */
    public function removeMember($enterprise, $username)
    {
        return $this->resProcess(Oauth::http_delete('https://gitee.com/api/v5/enterprises/' . $enterprise . '/members/' . $username . '?access_token=' . Oauth::getAccessToken()));
    }

    /**
     * 添加企业成员
     *
     * @param $enterprise
     * @param $username
     * @param $outsourced
     * @param $role
     * @param $name
     * @return array|mixed
     */
    public function addMember($enterprise, $username, $outsourced = false, $role = 'member', $name = '')
    {
        return $this->resProcess(Oauth::http_post('https://gitee.com/api/v5/enterprises/'. $enterprise . '/members?access_token=' . Oauth::getAccessToken(), array(
            'username' => $username,
            'outsourced' => $outsourced,
            'role' => $role,
            'name' => $name
        )));
    }


    public function updateMember($enterprise, $username, $outsourced, $role, $name, $active)
    {
        return $this->resProcess(Oauth::http_put('https://gitee.com/api/v5/enterprises/'.$enterprise.'/members/'.$username . '?access_token=' . Oauth::getAccessToken(), array(
            'outsourced' => $outsourced,
            'role' => $role,
            'name' => $name,
            'active' => $active
        )));
    }

    /**
     * 得到全部的成员
     *
     * @param $enterprise
     * @return array|mixed
     */
    public function getAllMembers($enterprise)
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/enterprises/' . $enterprise . '/members?access_token=' . Oauth::getAccessToken()));
    }


    /**
     * 得到全部的成员-用户名组成的一维数组
     *
     * @param $enterprise
     * @return array
     */
    public function getAllMembersToUserName($enterprise)
    {
        $allMembers = $this->getAllMembers($enterprise);

        $array = [];
        foreach ($allMembers as $member){
            $array[] = $member['user']['login'];
        }

        return $array;
    }


    /**
     * 是否是企业成员
     *
     * @param $enterprise
     * @param $username
     * @return bool
     */
    public function isMember($enterprise, $username)
    {
        $allMembers = $this->getAllMembersToUserName($enterprise);

        return in_array($username, $allMembers);
    }
}