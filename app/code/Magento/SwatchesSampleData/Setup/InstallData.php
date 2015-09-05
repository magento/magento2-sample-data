<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SwatchesSampleData\Setup;

use Magento\SwatchesSampleData\Model;
use Magento\Framework\Setup;

/**
 * Class InstallData
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var Model\Swatches;
     */
    protected $swatches;

    /**
     * @param Model\Swatches $swatches
     */
    public function __construct(Model\Swatches $swatches)
    {
        $this->swatches = $swatches;
    }

    /**
     * @inheritdoc
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->swatches->install();
    }
}
