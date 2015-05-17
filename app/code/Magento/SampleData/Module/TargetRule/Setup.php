<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\TargetRule;

use Magento\SampleData\Model\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for TargetRule module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for products
     *
     * @var Setup\Rule
     */
    protected $ruleSetup;

    /**
     * Constructor
     *
     * @param Setup\Rule $productSetup
     */
    public function __construct(
        Setup\Rule $productSetup
    ) {
        $this->ruleSetup = $productSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->ruleSetup->run();
    }
}
