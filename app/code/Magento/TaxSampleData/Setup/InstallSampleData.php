<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TaxSampleData\Setup;

use Magento\Framework\Setup;
use Magento\TaxSampleData\Model\Tax;

/**
 * Class Setup
 * Launches setup of sample data for Tax module
 */
class InstallSampleData implements SetupInterface
{
    /**
     * @var Tax
     */
    protected $tax;

    /**
     * @param Tax $tax
     */
    public function __construct(
        Tax $tax
    ) {
        $this->tax = $tax;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->tax->run(['Magento_TaxSampleData::fixtures/tax_rate.csv']);
    }
}
