<?php
/**
 * 设置表内置数据
 */

namespace BaseBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\FixtureInterface;
use RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools\Error;
use RedUnicorn\SymfonyKernel\Unicorn;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadTableData extends Unicorn  implements FixtureInterface, ContainerAwareInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
//        self::$conn = $this->container->get('database_connection');  //启用数据库组件
//        self::$error = new Error($this->container);  // 启用验证组件
    }
}