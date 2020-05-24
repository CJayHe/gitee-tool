<?php
/**
 * 表复制
 */

namespace RedUnicorn\SymfonyKernel\Model;

use BaseBundle\Controller\BaseController;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

class TableCopy extends BaseController
{
    public function copy($entity, $portion = 100)
    {
        $doctrine = $this->getDoctrine();

        /** @var ObjectManager|EntityManagerInterface $manager */
        $manager = $doctrine->getManager();
        $schemaManager = $doctrine->getConnection()->getSchemaManager();

        $metadata = $manager->getClassMetadata($entity);
        $table_name = $metadata->getTableName();

        for ($i = 1; $i <= $portion; $i++) {
            $table = $table_name . '_' . $i;
            $metadata->setPrimaryTable(array('name' => $table));

            $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($manager);

            if (!$schemaManager->tablesExist(array($table))) {
                $schemaTool->createSchema(array($metadata));
                echo $table . "创建成功<br>";
            } else {
                $schemaTool->updateSchema(array($metadata), true);
                echo $table . "更新成功<br>";
            }
        }
    }

    /**
     * 得到分表的名字
     *
     * @param $divide_mark
     * @param $table_name
     * @return string
     */
    static function getTableName($divide_mark, $table_name)
    {
        $divide_mark = explode('-', $divide_mark);
        $number = end($divide_mark);

        $number = $number % 100;

        if($number == 0){
            $number = 100;
        }

        return $table_name . '_' . $number;
    }
}