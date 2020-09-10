<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ConfigFields\Factory;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ViewModelFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $className): ArgumentInterface
    {
        return $this->objectManager->create($className);
    }
}
