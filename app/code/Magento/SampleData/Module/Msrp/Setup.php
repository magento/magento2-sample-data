<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Msrp;

use Magento\SampleData\Model\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for Msrp module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for Msrp
     *
     * @var Setup\Msrp
     */
    protected $msrp;

    /**
     * @param Setup\Msrp $msrp
     */
    public function __construct(
        Setup\Msrp $msrp
    ) {
        $this->msrp = $msrp;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->msrp->run();
    }
}
