<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Module\ConfigurableProduct\Setup\Product;

class Converter extends \Magento\Tools\SampleData\Module\Catalog\Setup\Product\Converter
{
    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix
     */
    protected $variationMatrix;

    /**
     * @param \Magento\Catalog\Api\CategoryManagementInterface $categoryReadService
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Product\Collection $productCollection
     * @param \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix
     */
    public function __construct(
        \Magento\Catalog\Api\CategoryManagementInterface $categoryReadService,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory $attributeCollectionFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Catalog\Model\Resource\Product\Collection $productCollection,
        \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix
    ) {
        $this->variationMatrix = $variationMatrix;
        parent::__construct(
            $categoryReadService,
            $eavConfig,
            $attributeCollectionFactory,
            $attrOptionCollectionFactory,
            $productCollection
        );
    }

    /**
     * @param array $row
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convertRow($row)
    {
        $data = parent::convertRow($row);

        if (!empty($data['configurable_attributes_data'])) {
            $data['configurable_attributes_data'] = $this->convertAttributesData($data['configurable_attributes_data']);
            if (!empty($data['associated_product_ids'])) {
                $data['associated_product_ids'] = $this->convertSkuToIds(
                    $this->getArrayValue($data['associated_product_ids'])
                );
            } else {
                $data['variations_matrix'] = $this->getVariationsMatrix($data);
            }
            $data['new_variations_attribute_set_id'] = $this->getAttributeSetId();
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function convertField(&$data, $field, $value)
    {
        if (in_array($field, ['color', 'size_general', 'size_pants', 'size_ball', 'size_strap'])) {
            if (!empty($value)) {
                $data['configurable_attributes_data'][$field] = $this->getArrayValue($value);
            }
            return true;
        }
        return false;
    }

    /**
     * @param array $configurableAttributes
     * @return array
     */
    protected function convertAttributesData($configurableAttributes)
    {
        $attributesData = [];
        foreach ($configurableAttributes as $attributeCode => $values) {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
            if (!$attribute->getId()) {
                continue;
            }
            $options = $this->getAttributeOptions($attribute->getAttributeCode());
            $attributeValues = [];
            $attributeOptions = [];
            list($values, $prices) = $this->convertAttributeValues($values);
            foreach ($options as $option) {
                $price = '';
                if (!empty($prices[$option->getValue()])) {
                    $price = $prices[$option->getValue()];
                }
                $attributeValues[] = [
                    'value_index' => $option->getId(),
                    'is_percent' => false,
                    'pricing_value' => $price,
                    'include' => (int)in_array($option->getValue(), $values),
                ];
                $attributeOptions[] = [
                    'value' => $option->getId(),
                    'label' => $option->getValue(),
                ];
            }
            $attributesData[$attribute->getId()] = [
                'id' => '',
                'label' => $attribute->getFrontend()->getLabel(),
                'use_default' => '',
                'position' => $attribute->getAttributeCode() == 'color' ? 10 : '',
                'attribute_id' => $attribute->getId(),
                'attribute_code' => $attribute->getAttributeCode(),
                'code' => $attribute->getAttributeCode(),
                'values' => $attributeValues,
                'options' => $attributeOptions,
            ];
        }
        return $attributesData;
    }

    /**
     * @param array $valuesData
     * @return array
     */
    protected function convertAttributeValues($valuesData)
    {
        $values = [];
        $prices = [];
        foreach ($valuesData as $item) {
            $itemData = explode(';', $item);
            if (!empty($itemData[0])) {
                $values[] = $itemData[0];
            }
            if (!empty($itemData[1])) {
                $prices[$itemData[0]] = $itemData[1];
            }
        }
        return [$values, $prices];
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getVariationsMatrix($data)
    {
        $variations = $this->variationMatrix->getVariations($data['configurable_attributes_data']);
        $result = [];
        $productPrice = 100;
        $productName = $data['name'];
        $productSku = $data['sku'];
        foreach ($variations as $variation) {
            $attributeValues = [];
            $attributeLabels = [];
            $price = $productPrice;
            foreach ($data['configurable_attributes_data'] as $attributeData) {
                $attributeId = $attributeData['attribute_id'];
                $attributeValues[$attributeData['attribute_code']] = $variation[$attributeId]['value'];
                $attributeLabels[$attributeData['attribute_code']] = $variation[$attributeId]['label'];
                if (isset($variation[$attributeId]['price'])) {
                    $priceInfo = $variation[$attributeId]['price'];
                    $price += ($priceInfo['is_percent'] ? $productPrice / 100.0 : 1.0) * $priceInfo['pricing_value'];
                }
            }
            $key = implode('-', $attributeValues);
            $result[$key] = [
                'image' => '',
                'name'   => $productName . '-' . implode('-', $attributeLabels),
                'sku'    => $productSku . '-' . implode('-', $attributeLabels),
                'configurable_attribute' => \json_encode($attributeValues),
                'quantity_and_stock_status' => ['qty' => '10'],
                'weight' => '1',
            ];
        }
        return $result;
    }

    /**
     * @param array $sku
     * @return array
     */
    protected function convertSkuToIds($sku)
    {
        $ids = [];
        foreach ($sku as $item) {
            $ids[] = $this->getProductIdBySku($item);
        }
        return $ids;
    }
}
