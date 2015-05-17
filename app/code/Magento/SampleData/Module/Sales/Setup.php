<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Sales;

use Magento\Framework\App\State;
use Magento\SampleData\Helper\PostInstaller;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * @var State
     */
    protected $appState;

    /**
     * Setup class for order
     *
     * @var Setup\Order
     */
    protected $orderSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param State $appState
     * @param Setup\Order $orderSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        State $appState,
        Setup\Order $orderSetup,
        PostInstaller $postInstaller
    ) {
        $this->appState = $appState;
        $this->orderSetup = $orderSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->addSetupResource($this->orderSetup);
    }
}
