<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesSampleData\Setup;

use Magento\Framework\Setup;
use Magento\SalesSampleData\Model;

/**
 * Class InstallData
 */
class InstallData implements Setup\InstallDataInterface
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
     * @inheritdoc
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->order->install(['Magento_SalesSampleData::fixtures/orders.csv']);
    }
}
