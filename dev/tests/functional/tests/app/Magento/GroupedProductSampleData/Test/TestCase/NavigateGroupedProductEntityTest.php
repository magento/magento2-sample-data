<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProductSampleData\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\GroupedProduct\Test\Fixture\GroupedProduct;

/**
 * @ZephyrId MAGETWO-33559
 * @group Sample_Data
 */
class NavigateGroupedProductEntityTest extends Injectable
{
    /* tags */
    const TEST_TYPE = 'acceptance_test';
    const MVP = 'yes';
    const MFTF_MIGRATED = 'yes';
    /* end tags */

    /**
     * Run test navigate products
     *
     * @param GroupedProduct $product
     * @return array
     */
    public function test(GroupedProduct $product)
    {
        return ['product' => $product];
    }
}
