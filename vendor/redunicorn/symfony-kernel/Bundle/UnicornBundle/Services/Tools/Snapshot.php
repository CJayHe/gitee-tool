<?php
/**
 * 快照
 */

namespace  RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Services\Tools;

use RedUnicorn\SymfonyKernel\Unicorn;

class Snapshot extends Unicorn
{
    public function setValue($key, $value)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $id = self::$conn->fetchColumn("SELECT id FROM snapshot WHERE config_key = ?", array($key));

        if(!empty($id)){
            $snapshot = $entityManager->getRepository(\RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Entity\Snapshot::class)->find($id);
            $snapshot->setConfigValue($value);

            $entityManager->flush();

        }else{
            $snapshot = new \RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Entity\Snapshot();
            $snapshot->setConfigValue($value);
            $snapshot->setConfigKey($key);

            $entityManager->persist($snapshot);
            $entityManager->flush();
        }
    }


    public function getValue($key, $def = '')
    {
        $val = self::$conn->fetchColumn("SELECT config_value FROM snapshot WHERE config_key = ?", array($key));

        if(empty($val)){
            $val = $def;
        }

        return $val;
    }

    public function has($key)
    {
        if(self::$conn->fetchColumn("SELECT 1 FROM snapshot WHERE config_key = ?", array($key))){
            return true;
        }

        return false;
    }
}
