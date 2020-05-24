<?php

/**
 * Session 改进
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Responses\Responses;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Acl\Exception\Exception;

class Sessions
{
    /**
     * session 对象
     *
     */
    private $session;

    /**
     * session 前缀
     *
     * @var mixed
     */
    private $prefix;

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * 存储方式
     *
     * @var mixed
     */
    private $session_type;

    /**
     * 存储方式
     *
     * @var mixed
     */
    private $redis_expire_time;

    /**
     * 初始化
     *
     * Sessions constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        if ($container->hasParameter('session_type') && $container->getParameter('session_type') == 'redis'){
            $this->session = $container->get('snc_redis.default');
            $this->session_type = 'redis';//存储方式
            $this->redis_expire_time = 60 * 60 * 24;//24小时
            if (array_key_exists('PHPREDISKEY',$_COOKIE)){
                $pre = $_COOKIE['PHPREDISKEY'];
            }elseif(isset($_POST['PHPREDISKEY'])){
                $pre = $_POST['PHPREDISKEY'];
            }elseif(isset($_GET['PHPREDISKEY'])){
                $pre = $_GET['PHPREDISKEY'];
            }else{
                $pre = 'PHPREDISKEY 为空';
            }
            $this->prefix = $container->hasParameter('prefix_session') ? $container->getParameter('prefix_session').$pre : 'prefix_redis_session_'.$pre;
        }else{
            $this->session = $container->get('session');
            $this->prefix = $container->hasParameter('prefix_session') ? $container->getParameter('prefix_session') : 'prefix_session';
        }
    }

    /**
     * session 设置多条数据
     *
     * @param mixed String|Array $data
     * @param String $value
     */
    public function set($data, $value = NULL)
    {
        if (is_array($data)) {
            foreach ($data as $sessionName => $sessionValue) {
                if ($this->session_type == 'redis'){
                    $this->session->set($this->prefix . $sessionName, json_encode($sessionValue));
                    if (!empty($this->redis_expire_time)){
                        $this->session->expire($this->prefix . $sessionName,$this->redis_expire_time);
                    }
                }else{
                    $this->session->set($this->prefix . $sessionName, $sessionValue);
                }
            }
        } else {
            if ($this->session_type == 'redis'){
                $this->session->set($this->prefix . $data, json_encode($value));
                if (!empty($this->redis_expire_time)){
                    $this->session->expire($this->prefix . $data,$this->redis_expire_time);
                }
            }else{
                $this->session->set($this->prefix . $data, $value);
            }
        }
    }

    /**
     * 判断session是否存在
     *
     * @param String $sessionName
     * @return boolean
     */
    public function has($sessionName)
    {
        if ($this->session_type == 'redis'){
            return $this->session->exists($this->prefix . $sessionName);
        }else{
            return $this->session->has($this->prefix . $sessionName);
        }
    }

    /**
     * 获取session值
     *
     * @param $sessionName
     * @return mixed
     */
    public function get($sessionName)
    {
        if ($this->session_type == 'redis'){
            return json_decode($this->session->get($this->prefix . $sessionName),true);
        }else{
            return $this->session->get($this->prefix . $sessionName);
        }
    }

    /**
     * 删除session值
     *
     * @param $sessionName
     */
    public function remove($sessionName)
    {
        if ($this->session_type == 'redis'){
            $this->session->del($this->prefix . $sessionName);
        }else{
            $this->session->remove($this->prefix . $sessionName);
        }
    }

    /**
     *  清楚session
     */
    public function clear()
    {
        $this->session->clear();
    }

    /**
     * 得到名字
     *
     * @param $sessionName
     * @return string
     */
    public function getName($sessionName)
    {
        return $this->prefix . $sessionName;
    }
}
