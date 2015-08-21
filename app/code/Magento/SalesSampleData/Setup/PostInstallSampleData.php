<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesSampleData\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\PostInstallSampleDataInterface;
use Magento\SalesSampleData\Model;

/**
 * Class PostInstallSampleData
 */
class PostInstallSampleData implements PostInstallSampleDataInterface
{
    /**
     * @var Model\Order;
     */
    protected $order;

    /**
     * @param Model\Order $order
     */
    public function __construct(Model\Order $order)
    {
        $this->order = $order;
    }

    /**
     * Installs optional data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $moduleContext
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $moduleContext)
    {
        $this->order->install(['Magento_SalesSampleData::fixtures/orders.csv']);
    }
}
