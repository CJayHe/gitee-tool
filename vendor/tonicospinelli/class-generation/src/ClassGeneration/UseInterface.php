<?php

/*
 * This file is part of the ClassGeneration package.
 *
 * (c) Antonio Spinelli <tonicospinelli@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClassGeneration;

use ClassGeneration\Element\AliasInterface;
use ClassGeneration\Element\ElementInterface;

/**
 * Use ClassGeneration
 * @author Antonio Spinelli <tonicospinelli@gmail.com>
 */
interface UseInterface extends ElementInterface, AliasInterface
{
    /**
     * Sets the class name.
     *
     * @param string $className
     *
     * @return UseInterface
     */
    public function setClassName($className);

    /**
     * Gets the class name.
     * @return string
     */
    public function getClassName();
}
