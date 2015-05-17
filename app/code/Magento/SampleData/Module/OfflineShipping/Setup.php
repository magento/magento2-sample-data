<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\OfflineShipping;

use Magento\SampleData\Model\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for OfflineShipping module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Tablerate
     */
    protected $tablerateSetup;

    /**
     * @param Setup\Tablerate $tablerateSetup
     */
    public function __construct(
        Setup\Tablerate $tablerateSetup
    ) {
        $this->tablerateSetup = $tablerateSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->tablerateSetup->run();
    }
}
