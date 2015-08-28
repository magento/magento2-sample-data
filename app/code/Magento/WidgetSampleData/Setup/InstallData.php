<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\WidgetSampleData\Setup;

use Magento\WidgetSampleData\Model\CmsBlock;
use Magento\Framework\Setup;

/**
 * Launches setup of sample data for Widget module
 */
class InstallSampleData implements Setup\InstallDataInterface
{
    /**
     * @var CmsBlock
     */
    protected $cmsBlock;

    /**
     * @param CmsBlock $cmsBlock
     */
    public function __construct(CmsBlock $cmsBlock) {
        $this->cmsBlock = $cmsBlock;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->cmsBlock->run(
            [
                'Magento_WidgetSampleData::fixtures/cmsblock.csv',
                'Magento_WidgetSampleData::fixtures/cmsblock_giftcard.csv'
            ]
        );
    }
}
