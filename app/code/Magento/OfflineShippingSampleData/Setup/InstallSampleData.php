<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\OfflineShippingSampleData\Setup;

use Magento\Framework\Setup;

/**
 * Class InstallSampleData
 */
class InstallSampleData implements SetupInterface
{
    /**
     * @var \Magento\OfflineShippingSampleData\Model\Tablerate
     */
    private $tablerate;

    /**
     * @param \Magento\OfflineShippingSampleData\Model\Tablerate $tablerate
     */
    public function __construct(\Magento\OfflineShippingSampleData\Model\Tablerate $tablerate) {
        $this->tablerate = $tablerate;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->tablerate->run(['Magento_OfflineShippingSampleData::fixtures/tablerate.csv']);
    }
}
