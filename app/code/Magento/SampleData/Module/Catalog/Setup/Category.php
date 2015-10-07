<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Catalog\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Category
 */
class Category implements SetupInterface
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\TreeFactory
     */
    protected $resourceCategoryTreeFactory;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Framework\Data\Tree\Node
     */
    protected $categoryTree;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\SampleData\Helper\Deploy
     */
    protected $deployHelper;

    /**
     * @var bool
     */
    protected $mediaInstalled;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $resourceCategoryTreeFactory
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param FixtureHelper $fixtureHelper
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\SampleData\Helper\Deploy $deployHelper
     * @param CsvReaderFactory $csvReaderFactory
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\TreeFactory $resourceCategoryTreeFactory,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        FixtureHelper $fixtureHelper,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\SampleData\Helper\Deploy $deployHelper,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resourceCategoryTreeFactory = $resourceCategoryTreeFactory;
        $this->moduleList = $moduleList;
        $this->storeManager = $storeManager;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->deployHelper = $deployHelper;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing categories:');
        $this->isMediaInstalled();
        foreach ($this->moduleList->getNames() as $moduleName) {
            $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/categories.csv';
            $fileName = $this->fixtureHelper->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                $this->createCategory($row);
            }
        }
    }

    /**
     * @param array $row
     * @param \Magento\Catalog\Model\Category $category
     * @return void
     */
    protected function setAdditionalData($row, $category)
    {
        $additionalAttributes = [
            'position',
            'display_mode',
            'page_layout',
            'custom_layout_update',
        ];

        foreach ($additionalAttributes as $categoryAttribute) {
            if (!empty($row[$categoryAttribute])) {
                $attributeData = [$categoryAttribute => $row[$categoryAttribute]];
                $category->addData($attributeData);
            }
        }
    }

    /**
     * Get category name by path
     *
     * @param string $path
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getCategoryByPath($path)
    {
        $names = array_filter(explode('/', $path));
        $tree = $this->getTree();
        foreach ($names as $name) {
            $tree = $this->findTreeChild($tree, $name);
            if (!$tree) {
                $tree = $this->findTreeChild($this->getTree(null, true), $name);
            }
            if (!$tree) {
                break;
            }
        }
        return $tree;
    }

    /**
     * Get child categories
     *
     * @param \Magento\Framework\Data\Tree\Node $tree
     * @param string $name
     * @return mixed
     */
    protected function findTreeChild($tree, $name)
    {
        $foundChild = null;
        if ($name) {
            foreach ($tree->getChildren() as $child) {
                if ($child->getName() == $name) {
                    $foundChild = $child;
                    break;
                }
            }
        }
        return $foundChild;
    }

    /**
     * Get category tree
     *
     * @param int|null $rootNode
     * @param bool $reload
     * @return \Magento\Framework\Data\Tree\Node
     */
    protected function getTree($rootNode = null, $reload = false)
    {
        if (!$this->categoryTree || $reload) {
            if ($rootNode === null) {
                $rootNode = $this->storeManager->getStore()->getRootCategoryId();
            }
            $tree = $this->resourceCategoryTreeFactory->create();
            $node = $tree->loadNode($rootNode)->loadChildren();

            $tree->addCollectionData(null, false, $rootNode);

            $this->categoryTree = $node;
        }
        return $this->categoryTree;
    }

    /**
     * @param array $row
     * @return void
     */
    protected function createCategory($row)
    {
        $category = $this->getCategoryByPath($row['path'] . '/' . $row['name']);
        if (!$category) {
            $parentCategory = $this->getCategoryByPath($row['path']);
            $active = $row['active']
                && (!isset($row['require_media']) || $this->isMediaInstalled() || !$row['require_media']);
            if (isset($row['require_media']) && $row['require_media'] == 2) {
                $active = 1;
                $row['display_mode'] = '';
                $row['page_layout'] = '';
            }
            $data = [
                'parent_id' => $parentCategory->getId(),
                'name' => $row['name'],
                'is_active' => $active,
                'is_anchor' => $row['is_anchor'],
                'include_in_menu' => $row['include_in_menu'],
                'url_key' => $row['url_key'],
            ];
            $category = $this->categoryFactory->create();
            $category->setData($data)
                ->setPath($parentCategory->getData('path'))
                ->setAttributeSetId($category->getDefaultAttributeSetId())
                ->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);
            $this->setAdditionalData($row, $category);
            $category->save();
            $this->logger->logInline('.');
        }
    }

    /**
     * @return bool
     */
    protected function isMediaInstalled()
    {
        if (!isset($this->mediaInstalled)) {
            $this->mediaInstalled = $this->deployHelper->isMediaPresent();
        }
        return $this->mediaInstalled;
    }
}
