<?php

namespace RedUnicorn\SymfonyKernel\Bundle\UnicornBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Snapshot
 *
 * @ORM\Table(name="snapshot", options={"comment":"快照表"})
 * @ORM\Entity
 */
class Snapshot
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="RedUnicorn\SymfonyKernel\Doctrine\SortIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="config_key", type="string", length=255, unique=true, options={"comment":"键"})
     */
    private $configKey;

    /**
     * @var string
     *
     * @ORM\Column(name="config_value", type="text", nullable=true, options={"comment":"值"})
     */
    private $configValue;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $configKey
     *
     * @return Snapshot
     */
    public function setConfigKey($configKey)
    {
        $this->configKey = $configKey;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfigKey()
    {
        return $this->configKey;
    }

    /**
     * @param $configValue
     *
     * @return Snapshot
     */
    public function setConfigValue($configValue)
    {
        $this->configValue = $configValue;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfigValue()
    {
        return $this->configValue;
    }
}
