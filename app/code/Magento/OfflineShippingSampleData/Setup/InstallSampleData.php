<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\OfflineShippingSampleData\Setup;

use Magento\Framework\Setup;
use Magento\OfflineShippingSampleData\Model\Tablerate;

/**
 * Class InstallSampleData
 */
class InstallSampleData implements Setup\InstallSampleDataInterface
{
    /**
     * @var Tablerate
     */
    private $tablerate;

    /**
     * @param Tablerate $tablerate
     */
    public function __construct(Tablerate $tablerate) {
        $this->tablerate = $tablerate;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->tablerate->install(['Magento_OfflineShippingSampleData::fixtures/tablerate.csv']);
    }
}
