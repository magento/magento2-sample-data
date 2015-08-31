<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ReviewSampleData\Setup;

use Magento\ReviewSampleData\Model\Review;
use Magento\Framework\Setup;

/**
 * Class InstallSampleData
 */
class InstallData implements Setup\InstallDataInterface
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
     * @inheritdoc
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->review->install(['Magento_ReviewSampleData::fixtures/products_reviews.csv']);
    }
}
