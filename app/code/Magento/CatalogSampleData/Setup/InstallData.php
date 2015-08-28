<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSampleData\Setup;

use Magento\Framework\Setup;

/**
 * Class Setup
 * Launches setup of sample data for catalog module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Setup class for category
     *
     * @var \Magento\CatalogSampleData\Model\Category
     */
    protected $categorySetup;

    /**
     * Setup class for product attributes
     *
     * @var \Magento\CatalogSampleData\Model\Attribute
     */
    protected $attributeSetup;

    /**
     * Setup class for products
     *
     * @var \Magento\CatalogSampleData\Model\Product
     */
    protected $productSetup;

    /**
     * Setup class for products
     *
     * @var \Magento\CatalogSampleData\Model\ProductLink
     */
    protected $productLinkSetup;

    /**
     * @param \Magento\CatalogSampleData\Model\Category $categorySetup
     * @param \Magento\CatalogSampleData\Model\Attribute $attributeSetup
     * @param \Magento\CatalogSampleData\Model\Product $productSetup
     * @param \Magento\CatalogSampleData\Model\ProductLink $productLinkSetup
     */
    public function __construct(
        \Magento\CatalogSampleData\Model\Category $categorySetup,
        \Magento\CatalogSampleData\Model\Attribute $attributeSetup,
        \Magento\CatalogSampleData\Model\Product $productSetup,
        \Magento\CatalogSampleData\Model\ProductLink $productLinkSetup
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
        $this->productSetup = $productSetup;
        $this->productLinkSetup = $productLinkSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $context)
    {
        $this->attributeSetup->run(['Magento_CatalogSampleData::fixtures/attributes.csv']);
        $this->categorySetup->run(['Magento_CatalogSampleData::fixtures/categories.csv']);
        $this->productSetup->run(
            [
                'Magento_CatalogSampleData::fixtures/SimpleProduct/products_gear_bags.csv',
                'Magento_CatalogSampleData::fixtures/SimpleProduct/products_gear_fitness_equipment.csv',
                'Magento_CatalogSampleData::fixtures/SimpleProduct/products_gear_fitness_equipment_ball.csv',
                'Magento_CatalogSampleData::fixtures/SimpleProduct/products_gear_fitness_equipment_strap.csv',
                'Magento_CatalogSampleData::fixtures/SimpleProduct/products_gear_watches.csv',
            ],
            [
                'Magento_CatalogSampleData::fixtures/SimpleProduct/images_gear_bags.csv',
                'Magento_CatalogSampleData::fixtures/SimpleProduct/images_gear_fitness_equipment.csv',
                'Magento_CatalogSampleData::fixtures/SimpleProduct/images_gear_watches.csv',
            ]
        );

        //$this->postInstaller->addSetupResource($this->productLinkSetup);
    }
}
