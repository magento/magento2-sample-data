<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalanceSampleData\Model\Plugin;

/**
 * Class Observer
 */
class CustomerBalanceRefund
{
    /**
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @return mixed
     */
    public function aroundGetCreditmemoData(
        \Magento\SalesSampleData\Model\Order\Processor $subject,
        \Closure $proceed,
        \Magento\Sales\Model\Order\Item $orderItem
    ) {
        $data = $proceed($orderItem);
        if ($orderItem->getOrder()->getBaseGrandTotal()) {
            $data['refund_customerbalance_return_enable'] = '1';
            $data['refund_customerbalance_return'] = $orderItem->getOrder()->getBaseGrandTotal();
        }

        return $data;
    }
}
