<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerBalanceSampleData\Model;

/**
 * Class Observer
 */
class Observer
{
    /**
     * @param \Magento\Framework\DataObject $params
     * @return mixed
     */
    public function getCreditmemoData(\Magento\Framework\DataObject $params)
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $params->getOrderItem();
        $data = $params->getCreditMemo();
        if ($orderItem->getOrder()->getBaseGrandTotal()) {
            $data['refund_customerbalance_return_enable'] = '1';
            $data['refund_customerbalance_return'] = $orderItem->getOrder()->getBaseGrandTotal();
        }

        return $data;
    }
}
