<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSampleData\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Mtf\Constraint\AbstractConstraint;

/**
 * Assert that customer navigation menu links can be opened.
 */
class AssertCustomerNavigation extends AbstractConstraint
{
    /**
     * Assert that customer navigation menu links can be opened.
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param array $customerNavigationLinks
     * @return void
     */
    public function processAssert(CustomerAccountIndex $customerAccountIndex, array $customerNavigationLinks)
    {
        $actualPageTitles = [];
        $expectedPageTitles = [];
        /** @var \Magento\Customer\Test\Block\Account\Links $accountMenu */
        $accountMenu = $customerAccountIndex->getAccountMenuBlock();
        /** @var \Magento\Theme\Test\Block\Html\Title $titleBlock */
        $titleBlock = $customerAccountIndex->getTitleBlock();

        foreach ($customerNavigationLinks as $link) {
            $expectedPageTitles[] = $link['pageTitle'];
            $accountMenu->openMenuItem($link['navigationName']);
            $actualPageTitles[] = $titleBlock->getTitle();
        }

        \PHPUnit\Framework\Assert::assertEquals(
            $expectedPageTitles,
            $actualPageTitles,
            "Page titles are different."
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return "Customer is successfully navigating in account menu.";
    }
}
