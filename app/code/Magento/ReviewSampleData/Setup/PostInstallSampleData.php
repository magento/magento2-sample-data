<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ReviewSampleData\Setup;

use Magento\ReviewSampleData\Model\Review;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\PostInstallSampleDataInterface;

/**
 * Class InstallSampleData
 */
class PostInstallSampleData implements PostInstallSampleDataInterface
{
    /**
     * @var Review
     */
    protected $review;

    /**
     * @param Review $review
     */
    public function __construct(Review $review)
    {
        $this->reviewSetup = $review;
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
        $this->review->run(['Magento_ReviewSampleData::fixtures/products_reviews.csv']);
    }
}
