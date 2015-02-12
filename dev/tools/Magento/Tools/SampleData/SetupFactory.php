<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\ObjectManagerInterface;

class SetupFactory
{
    const INSTANCE_TYPE = 'Magento\Tools\SampleData\SetupInterface';

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param string $resourceType
     * @return SetupInterface
     * @throws \LogicException
     */
    public function create($resourceType)
    {
        if (false == is_subclass_of($resourceType, self::INSTANCE_TYPE) && $resourceType !== self::INSTANCE_TYPE) {
            throw new \LogicException($resourceType . ' is not a ' . self::INSTANCE_TYPE);
        }

        return $this->_objectManager->create($resourceType);
    }
}
