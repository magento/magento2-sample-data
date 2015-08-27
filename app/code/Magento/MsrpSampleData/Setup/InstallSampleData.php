<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MsrpSampleData\Setup;

use Magento\Framework\Setup;
/**
 * Class Setup
 * Launches setup of sample data for Msrp module
 */
class InstallData implements Setup\InstallDataInterface{
    /**
     * Setup class for Msrp
     *
     * @var \Magento\MsrpSampleData\Model\Msrp
     */
    protected $msrp;

    /**
     * @param \Magento\MsrpSampleData\Model\Msrp $msrp
     */
    public function __construct(
        \Magento\MsrpSampleData\Model\Msrp $msrp
    ) {
        $this->msrp = $msrp;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->msrp->run(['MsrpSampleData::fixtures/products_msrp.csv']);
    }
}
