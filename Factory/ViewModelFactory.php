<?php
/**
 * See LICENSE.md for license details.
 */
namespace Netresearch\ConfigFields\Factory;

use Magento\Framework\ObjectManagerInterface;
use TypeDuplication\ArgumentInterface;

/**
 * Class ViewModelFactory
 *
 * @package Netresearch\ConfigFields\Factory
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class ViewModelFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * ViewModelFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $className
     * @return ArgumentInterface|null
     */
    public function create(string $className)
    {
        if (class_exists($className)) {
            return $this->objectManager->create($className);
        }

        return null;
    }
}
