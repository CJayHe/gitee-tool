<?php
/**
 * 核心类
 */

namespace RedUnicorn\SymfonyKernel;

use Doctrine\DBAL\Connection;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Request\Request;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools\Error;
use RedUnicorn\SymfonyKernel\Exception\UnicornException;
use RedUnicorn\SymfonyKernel\Model\Model;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\DataCollectorTranslator;

abstract class Unicorn extends Controller
{
    public function __construct(ContainerInterface $container = null)
    {
        header("Content-Type: text/html; charset=utf-8");
        header('Access-Control-Allow-Origin: *');
        $this->setContainer($container);
    }

    function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        if (!empty($container)){
            //跨域访问的时候才会存在此字段
            $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
            $site_mark = $container->hasParameter('site_mark') ? $container->getParameter('site_mark') : 'master';

            if ($site_mark == 'master'){
                $allow_origin = $container->hasParameter('cross-domain_url') ? $container->getParameter('cross-domain_url'):[];
            }else{
                $allow_origin = $container->hasParameter('beta-cross-domain_url') ? $container->getParameter('beta-cross-domain_url'):[];
            }
            $origin = strtolower($origin);

            if(!empty($origin) && !empty($allow_origin) && in_array($origin, $allow_origin)){
                header('Access-Control-Allow-Origin:'.$origin);

                //session跨域需设置未true 且Access-Control-Allow-Origin不能未*
                header('Access-Control-Allow-Credentials:true');

                header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
                // 响应类型
                header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE,PATCH,OPTIONS");
                // 响应头设置
                header('Access-Control-Allow-Headers:x-requested-with,Content-Type, Accept, Authorization');
            }
        }
    }

    /**
     * 全局返回值
     *
     * @var array
     */
    private static $global_return;

    /**
     * 返回类型
     *
     * @var string
     */
    private static $return_type;


    //TODO 常用服务

    /**
     * @var Connection
     */
    protected static $conn = null;

    /**
     * @var Request
     */
    protected static $request = null;

    /**
     * @var Error
     */
    protected static $error = null;

    /**
     * @var DataCollectorTranslator
     */
    protected static $trans = null;

    /**
     * 设置return_type
     *
     * @param $return_type
     */
    final public static function setReturnType($return_type)
    {
        if(in_array($return_type, ['data', 'page'])) {
            self::$return_type = $return_type;
        }else{
            throw new UnicornException('return_type 溢出');
        }
    }

    /**
     * 得到return_type
     *
     * @return mixed
     */
    final public static function getReturnType()
    {
        return self::$return_type;
    }

    /**
     * 得到全局返回值
     *
     * @param $key
     * @return array
     */
    final public static function getGlobalReturn($key = null)
    {
        if(empty($key)){
            return self::$global_return;
        }else{
            return self::$global_return[$key];
        }
    }

    /**
     * 设置全局返回值
     *
     * @param $key
     * @param $value
     */
    final public static function setGlobalReturn($key, $value)
    {
        self::$global_return[$key] = $value;
    }

    /**
     * 得到URL
     *
     * @param string $route  路径名
     * @param array $parameters  路径参数
     * @return string
     */
    function getUrl($route, $parameters = array())
    {
        return self::$request->getSchemeAndHttpHost()  . $this->generateUrl($route, $parameters);
    }

    /**
     * 跳转url
     *
     * @param $url
     */
    public function goUrl($url)
    {
        Header("Location:{$url}");exit;
    }

    /**
     * 跳转路由
     *
     * @param $route
     * @param array $parameters
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function goRoute($route, $parameters = array(), $status = 302)
    {
        return $this->redirectToRoute($route, $parameters, $status);
    }

    /**
     * Gets a parameter.
     *
     * @param string $name The parameter name
     *
     * @return mixed The parameter value
     */
    protected function getParameter($name)
    {
        $parameter = $this->container->getParameter($name);

        if($this->container->hasParameter('trans_parameters') && is_array($parameter) && in_array($name, $this->container->getParameter('trans_parameters')))
        {
            foreach ($parameter as $key => $value) {
                if(is_array($value)){
                    throw new \InvalidArgumentException('翻译系统无法正常工作,配置参数需为二维数组');
                }

                $parameter[$key] = self::$trans->trans($value);
            }
        }

        return $parameter;
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        $parameters['session_type'] = $this->container->hasParameter('session_type') ? $this->getParameter('session_type') : '';

        return parent::render($view, $parameters, $response);
    }
}