<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2019/1/8
 */

namespace Gitee;


class User
{
    /**
     * 搜索用户
     *
     * @param string $q 关键词
     * @param int $page
     * @param int $per_page 每页行数  最大100
     * @return array|mixed
     */
    public function searchUser($q, $page = 1, $per_page = 10)
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/search/users?access_token=' . Oauth::getAccessToken() . '&q=' . $q . '&page=' . $page . '&pre_page='. $per_page));
    }

    /**
     * 得到授权用户信息
     */
    public function getInfo()
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/user?access_token='. Oauth::getAccessToken()));
    }

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
}