<?php
/**
* 请求封装
*/

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Request;

use RedUnicorn\SymfonyKernel\Appoint\Adorner\RequestAfterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Request extends \Symfony\Component\HttpFoundation\Request
{
    private $requestAfters = array();

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest()->request;
        $this->query = $request->getCurrentRequest()->query;
        $this->files = $request->getCurrentRequest()->files;
        $this->server = $request->getCurrentRequest()->server;
        $this->cookies = $request->getCurrentRequest()->cookies;
        $this->session = $request->getCurrentRequest()->session;
        $this->attributes = $request->getCurrentRequest()->attributes;
        $this->headers = $request->getCurrentRequest()->headers;
    }

    public function get($key, $default = null, $unfasten = [] , $deep = false)
    {
        $value =  parent::get($key, $default, $deep);

        foreach ($this->requestAfters as $requestAfter){
           $value = $requestAfter->after($key, $value , $unfasten);
        }

        return trim($value);
    }

    public function addRequestAfter(RequestAfterInterface $requestAfter)
    {
        $this->requestAfters[] = $requestAfter;
    }

    public function getRequestAfters()
    {
        $data = [];

        foreach ($this->requestAfters as $requestAfter){
            $data[$requestAfter->dir()] = $requestAfter->register();
        }

        return $data;
    }

    /**
     * 获得上一级url
     *
     * @return array|string
     */
    final public function getForwardUrl()
    {
        return $this->headers->get('referer');
    }

    /**
     * 得到当前的URL
     *
     * @return string
     */
    final public function getCurrentUrl()
    {
        //判断是http还是https
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        //全路径
        return $http_type . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获得当前路由名
     *
     * @return mixed
     */
    final public function getCurrentRouteName()
    {
        return $this->get('_route');
    }

    /**
     * 判断是否ajax请求
     *
     * @return bool
     */
    final public function is_ajax() {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']))
                return true;
        }

        return false;
    }
}