<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\GiftCard\Setup\Product;

/**
 * Class Converter
 */
class Converter extends \Magento\SampleData\Module\Catalog\Setup\Product\Converter
{
    /**
     * @inheritdoc
     */
    protected function convertField(&$data, $field, $value)
    {
        $weight = 1;
        if ($field == 'price') {
            $data = $this->getAmountValues($data, $value);
            return true;
        }
        if ($field == 'format') {
            switch ($value) {
                case 'Virtual':
                    $data['giftcard_type'] = \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL;
                    break;
                case 'Physical':
                    $data['giftcard_type'] = \Magento\GiftCard\Model\Giftcard::TYPE_PHYSICAL;
                    $data['weight'] = $weight;
                    break;
                case 'Combined':
                    $data['giftcard_type'] = \Magento\GiftCard\Model\Giftcard::TYPE_COMBINED;
                    $data['weight'] = $weight;
                    break;
            }
            return true;
        }
        return false;
    }

    /**
     * @param array $data
     * @param mixed $value
     * @return mixed
     */
    protected function getAmountValues($data, $value)
    {
        $prices = $this->getArrayValue($value);
        $i = -1;
        foreach ($prices as $price) {
            if (is_numeric($price)) {
                $data['giftcard_amounts'][++$i]['website_id'] = 0;
                $data['giftcard_amounts'][$i]['price'] = $price;
                $data['giftcard_amounts'][$i]['delete'] = null;
            } elseif ($price == 'Custom') {
                $data['allow_open_amount'] = \Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_ENABLED;
                $data['open_amount_min'] = min($prices);
                $data['open_amount_max'] = null;
            }
        }

        return $data;
    }
}
