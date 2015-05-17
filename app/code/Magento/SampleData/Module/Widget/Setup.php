<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Module\Widget;

use Magento\SampleData\Helper\PostInstaller;
use Magento\SampleData\Model\SetupInterface;

/**
 * Launches setup of sample data for Widget module
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\CmsBlock
     */
    protected $cmsBlockSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param PostInstaller $postInstaller
     * @param Setup\CmsBlock $cmsBlockSetup
     */
    public function __construct(
        PostInstaller $postInstaller,
        Setup\CmsBlock $cmsBlockSetup
    ) {
        $this->postInstaller = $postInstaller;
        $this->cmsBlockSetup = $cmsBlockSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->addSetupResource($this->cmsBlockSetup, 20);
    }
}
