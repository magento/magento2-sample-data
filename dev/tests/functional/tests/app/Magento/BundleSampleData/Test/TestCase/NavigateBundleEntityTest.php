<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleSampleData\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\Bundle\Test\Fixture\BundleProduct;

/**
 * @ZephyrId MAGETWO-33559
 * @group Sample_Data
 */
class NavigateBundleEntityTest extends Injectable
{
    /* tags */
    const TEST_TYPE = 'acceptance_test';
    const MVP = 'yes';
    const MFTF_MIGRATED = 'yes';
    /* end tags */

    /**
     * Run test navigate products
     *
     * @param BundleProduct $product
     * @return array
     */
    public function test(BundleProduct $product)
    {
        return ['product' => $product];
    }
}
