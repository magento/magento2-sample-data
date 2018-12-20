<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSampleData\Test\TestCase;

use Magento\Mtf\TestCase\Injectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * @ZephyrId MAGETWO-33559
 * @group Sample_Data
 */
class NavigateProductEntityTest extends Injectable
{
    /* tags */
    const TEST_TYPE = 'acceptance_test';
    const MVP = 'yes';
    const MFTF_MIGRATED = 'yes';
    /* end tags */

    /**
     * Run test navigate products.
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    public function test(CatalogProductSimple $product)
    {
        return ['product' => $product];
    }
}
