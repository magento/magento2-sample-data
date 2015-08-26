<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableSampleData\Setup;

use Magento\Framework\Setup;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var \Magento\CatalogSampleData\Model\Attribute
     */
    private $attribute;

    /**
     * @var \Magento\CatalogSampleData\Model\Category
     */
    private $category;

    /**
     * @var \Magento\ConfigurableSampleData\Model\Product
     */
    private $configurableProduct;

    /**
     * @var \Magento\SalesSampleData\Model\Order
     */
    private $order;

    /**
     * @param \Magento\CatalogSampleData\Model\Attribute $attribute
     * @param \Magento\CatalogSampleData\Model\Category $category
     * @param \Magento\ConfigurableSampleData\Model\Product $configurableProduct
     * @param \Magento\SalesSampleData\Model\Order $order
     */
    public function __construct(
        \Magento\CatalogSampleData\Model\Attribute $attribute,
        \Magento\CatalogSampleData\Model\Category $category,
        \Magento\ConfigurableSampleData\Model\Product $configurableProduct,
        \Magento\SalesSampleData\Model\Order $order
    ) {
        $this->attribute = $attribute;
        $this->category = $category;
        $this->configurableProduct = $configurableProduct;
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->attribute->install(['Magento_ConfigurableProduct::fixtures/attributes.csv']);
        $this->category->install(['Magento_ConfigurableProduct::fixtures/categories.csv']);
        $this->configurableProduct->install(
            [
                'Magento_ConfigurableSampleData::fixtures/products_men_tops.csv',
                'Magento_ConfigurableSampleData::fixtures/products_men_bottoms.csv',
                'Magento_ConfigurableSampleData::fixtures/products_women_tops.csv',
                'Magento_ConfigurableSampleData::fixtures/products_women_bottoms.csv',
                'Magento_ConfigurableSampleData::fixtures/products_gear_fitness_equipment_ball.csv',
                'Magento_ConfigurableSampleData::fixtures/products_gear_fitness_equipment_strap.csv',
                'Magento_ConfigurableSampleData::fixtures/products_women_tops_required.csv',
            ],
            [
                'Magento_ConfigurableSampleData::fixtures/images_men_bottoms.csv',
                'Magento_ConfigurableSampleData::fixtures/images_men_tops.csv',
                'Magento_ConfigurableSampleData::fixtures/images_women_bottoms.csv',
                'Magento_ConfigurableSampleData::fixtures/images_women_tops.csv',
                'Magento_ConfigurableSampleData::fixtures/images_gear_fitness_equipment.csv',
            ]
        );

        //@todo implement this
        //$this->order->install(['Magento_ConfigurableProduct::fixtures/orders.csv']);
    }
}
