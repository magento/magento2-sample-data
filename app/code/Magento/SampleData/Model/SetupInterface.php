<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData;

interface SetupInterface
{
    /**
     * Runs sample data setup process for some module
     *
     * @return void
     */
    public function run();
}
