<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MsrpSampleData\Setup;

use Magento\MsrpSampleData\Model\Msrp;
use Magento\Framework\Setup;

/**
 * Class Setup
 * Launches setup of sample data for Msrp module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Setup class for Msrp
     *
     * @var Msrp
     */
    protected $msrp;

    /**
     * @param Msrp $msrp
     */
    public function __construct(Msrp $msrp) {
        $this->msrp = $msrp;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->msrp->install(['MsrpSampleData::fixtures/products_msrp.csv']);
    }
}
