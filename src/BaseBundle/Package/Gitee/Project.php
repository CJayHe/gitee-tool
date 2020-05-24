<?php
/**
 * PhpStorm.
 * User: Jay
 * Date: 2018/11/20
 */

namespace Gitee;


class Project
{
    /**
     * 得到全部的项目
     *
     * @param int $page
     * @param int $rows
     * @return mixed
     */
    public function allProject($page = 1, $rows = 20)
    {
        return json_decode(Oauth::http_get('https://gitee.com/api/v5/user/repos?access_token=' . Oauth::getAccessToken() . '&sort=full_name&page=' . $page .'&per_page=' . $rows), true);
    }

    /**
     * owmer/repo  形如 yayuanzi/790-houduan
     *
     * @param $owner
     * @param $repo
     * @param int $page
     * @param int $rows
     * @param string $sha
     * @return array
     */
    public function getProjectCommits($owner, $repo, $sha = 'master', $page = 1, $rows = 20)
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/repos/' . $owner . '/' . $repo . '/commits?access_token=' . Oauth::getAccessToken() . '&sha=' . $sha . '&sort=full_name&page=' . $page . '&per_page=' . $rows));
    }

    /**
     * 添加项目合作者（添加成员）
     *
     * @param $owner
     * @param $repo
     * @param $username
     * @param string $permission
     * @return array|mixed
     */
    public function addCollaborators($owner, $repo, $username, $permission = 'push')
    {
        return $this->resProcess(Oauth::http_put('https://gitee.com/api/v5/repos/' .$owner . '/' . $repo .'/collaborators/' . $username . '?access_token=' . Oauth::getAccessToken(). '&permission='.$permission));
    }

    /**
     * 移除项目合作者(添加成员)
     *
     * @param $owner
     * @param $repo
     * @param $username
     * @return array|mixed
     */
    public function removeCollaborators($owner, $repo, $username)
    {
        return $this->resProcess(Oauth::http_delete('https://gitee.com/api/v5/repos/' .$owner . '/' . $repo .'/collaborators/' . $username . '?access_token=' . Oauth::getAccessToken()));
    }

    /**
     * 得到全部的成员信息
     *
     * @param $owner
     * @param $repo
     * @return array|mixed
     */
    public function getAllCollaborators($owner, $repo)
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/repos/' .$owner . '/' . $repo .'/collaborators?access_token=' . Oauth::getAccessToken()));
    }

    /**
     * owmer/repo  形如 yayuanzi/790-houduan
     *
     * @param $owner
     * @param $repo
     * @return array|mixed
     */
    public function getProjectBranches($owner, $repo)
    {
        return $this->resProcess(Oauth::http_get('https://gitee.com/api/v5/repos/' . $owner . '/' . $repo .'/branches?access_token=' . Oauth::getAccessToken()));
    }

    /**
     * 创建企业项目仓库 -- TODO 尚不开发
     *
     * @param $enterprise
     * @return array|mixed
     */
    public function createProject($enterprise)
    {
        return $this->resProcess(array());
    }

    /**
     * 删除企业项目 -- TODO 尚不开发
     *
     * @param $owner
     * @param $repo
     * @param $id
     * @return array|mixed
     */
    public function delProject($owner, $repo, $id)
    {
        return $this->resProcess(Oauth::http_delete('https://gitee.com/api/v5/repos/'. $owner .'/ ' .$repo. '/releases/' . $id .'?access_token=' . Oauth::getAccessToken()));
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

    /**
     * git 首页地址分解
     *
     * @param $index_url
     * @return array
     */
    public static function indexUrlAnalysis($index_url)
    {
        $index_url_arr = explode('/', $index_url);
        $count = count($index_url_arr);

        $info['owner'] = array_key_exists($count - 2, $index_url_arr) ? $index_url_arr[$count - 2] : '';
        $info['repo'] = array_key_exists($count - 1, $index_url_arr) ? $index_url_arr[$count - 1] : '';

        return $info;
    }

    /**
     * 得到 ssh 克隆地址
     *
     * @param $owner
     * @param $repo
     * @return string
     */
    public static function getSSHCloneUrl($owner, $repo)
    {
        return 'git@gitee.com:' .$owner .'/' . $repo . '.git';
    }

    /**
     * 得到 https 克隆地址
     *
     * @param $owner
     * @param $repo
     * @return string
     */
    public static function getHttpsCloneUrl($owner, $repo)
    {
        return 'https://gitee.com/' . $owner .'/' . $repo . '.git';
    }

    /**
     * 验证首页地址是否合法
     *
     * @param $index_url
     * @return bool
     */
    public function verifyIndexUrl($index_url)
    {
        $info = self::indexUrlAnalysis($index_url);

        if(empty($this->getProjectBranches($info['owner'], $info['repo']))) {
            return false;
        }else{
            return true;
        }
    }

}