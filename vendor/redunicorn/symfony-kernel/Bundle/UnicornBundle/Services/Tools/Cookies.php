<?php

/**
 * Cookies 改进类
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class Cookies
{
    /**
     * cookies 对象
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    private $cookies;

    /**
     * cookies 前缀
     *
     * @var mixed
     */
    private $prefix;

    /**
     * 初始化
     *
     * Cookies constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->cookies = $container->get('unicorn.request')->cookies;
        $this->prefix = $container->hasParameter('prefix_cookie') ? $container->getParameter('prefix_cookie') : 'prefix_cookie';
    }

    /**
     * session 设置多条数据
     *
     * @param $data
     * @param null $value
     * @param int $expire
     */
    public function set($data, $value = NULL, $expire = 0)
    {

        $response = new Response();

        if (is_array($data)) {
            foreach ($data as $cookieName => $cookieValue) {
                // $this->cookies->set($cookieName, $cookieValue);
                $expire = $expire ? time() + $expire : 0;
                $cookie = new Cookie($this->prefix . $cookieName, $cookieValue, $expire, '/');
                $response->headers->setCookie($cookie);
            }
        } else {
            // $this->cookies->set($data, $value);
            $expire = $expire ? time() + $expire : 0;
            $cookie = new Cookie($this->prefix . $data, $value, $expire, '/');
            $response->headers->setCookie($cookie);
        }


        $response->send();
    }
    
    /**
     * 判断cookie是否存在
     * 
     * @param String $cookieName
     * @return boolean
     */
    public function has($cookieName)
    {
        return $this->cookies->has($this->prefix . $cookieName);
    }

    /**
     * 获取cookie值
     *
     * @param $cookieName
     * @return mixed
     */
    public function get($cookieName)
    {
        return $this->cookies->get($this->prefix . $cookieName);
    }

    /**
     * 获取所有cookie值
     *
     * @return array
     */
    public function getAll()
    {
        return $this->cookies->all();
    }

    /**
     * 删除cookie值
     *
     * @param $cookieName
     */
    public function remove($cookieName)
    {
        $this->cookies->remove($this->prefix . $cookieName);
    }

    /**
     *  清除cookie
     */
    public function clear()
    {
        $cookies = $this->getAll();
        foreach ($cookies as $cookieName => $cookieValue) {
            $this->cookies->remove($this->prefix . $cookieName);
        }
    }
}
