<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData\Module\Catalog\Setup;

use Magento\Tools\SampleData\TestLogger;

/**
 * Class ProductTest
 */
class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        $this->installAttributes();
        $this->installCategories();
        $this->installProducts();
    }

    public function installAttributes()
    {
        $fixtureHelper = $this->getMockBuilder('Magento\Tools\SampleData\Helper\Fixture')
            ->disableOriginalConstructor()->setMethods(['getPath'])
            ->getMock();

        $fixtureHelper->expects($this->at(0))->method('getPath')->will($this->returnValue(
            realpath(__DIR__ . '/../../../_files/catalog_attributes.csv')
        ));

        /** @var \Magento\Tools\SampleData\Module\Catalog\Setup\Attribute $attributes */
        $attributes = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Catalog\Setup\Attribute',
            [
                'fixtureHelper' => $fixtureHelper,
                'logger' => TestLogger::factory(),
            ]
        );

        ob_start();
        $attributes->run();
        $result = ob_get_clean();
        $this->assertContains('Installing catalog attributes', $result);
        $this->assertContains('.................', $result);
    }

    public function installCategories()
    {
        /** @var \Magento\Tools\SampleData\Module\Catalog\Setup\Category $categories */
        $categories = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Catalog\Setup\Category',
            ['logger' => TestLogger::factory()]
        );

        ob_start();
        $categories->run();
        $result = ob_get_clean();
        $this->assertContains('Installing categories', $result);
        $this->assertContains('......................................', $result);
    }

    public function installProducts()
    {
        $fixtureHelper = $this->getMockBuilder('Magento\Tools\SampleData\Helper\Fixture')
            ->disableOriginalConstructor()->setMethods(['getPath'])
            ->getMock();
        $fixtures = [realpath(__DIR__ . '/../../../_files/catalog_product.csv')];
        $fixtureHelper->expects($this->at(0))->method('getPath')->with($fixtures[0])
            ->will($this->returnValue($fixtures[0]));
        /** @var \Magento\Tools\SampleData\Module\Catalog\Setup\Product $products */
        $products = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Catalog\Setup\Product',
            [
                'fixtureHelper' => $fixtureHelper,
                'fixtures' => $fixtures,
                'logger' => TestLogger::factory(),
            ]
        );

        ob_start();
        $products->run();
        $result = ob_get_clean();
        $this->assertContains('Installing simple products', $result);
        $this->assertContains('.', $result);
    }
}
