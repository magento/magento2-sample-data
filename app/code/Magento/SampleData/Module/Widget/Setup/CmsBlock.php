<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Module\Widget\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Launches setup of sample data for Widget module
 */
class CmsBlock implements SetupInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $widgetFactory;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    protected $themeCollectionFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $cmsBlockFactory;

    /**
     * @var \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory
     */
    protected $appCollectionFactory;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $themeCollectionFactory
     * @param \Magento\Cms\Model\BlockFactory $cmsBlockFactory
     * @param \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $appCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     * @param \Magento\SampleData\Model\Logger $logger
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $themeCollectionFactory,
        \Magento\Cms\Model\BlockFactory $cmsBlockFactory,
        \Magento\Widget\Model\ResourceModel\Widget\Instance\CollectionFactory $appCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\SampleData\Model\Logger $logger,
        $fixtures = []
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->widgetFactory = $widgetFactory;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->cmsBlockFactory = $cmsBlockFactory;
        $this->appCollectionFactory = $appCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->fixtures = $fixtures;
        if (empty($this->fixtures)) {
            $this->fixtures = $this->fixtureHelper->getDirectoryFiles('Widget');
        }
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing Widgets:');
        $pageGroupConfig = [
            'pages' => [
                'block' => '',
                'for' => 'all',
                'layout_handle' => 'default',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => '',
            ],
            'all_pages' => [
                'block' => '',
                'for' => 'all',
                'layout_handle' => 'default',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => '',
            ],
            'anchor_categories' => [
                'entities' => '',
                'block' => '',
                'for' => 'all',
                'is_anchor_only' => 0,
                'layout_handle' => 'catalog_category_view_type_layered',
                'template' => 'widget/static_block/default.phtml',
                'page_id' => '',
            ],
        ];

        foreach ($this->fixtures as $file) {
            $fileName = $this->fixtureHelper->getPath($file);
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                /** @var \Magento\Widget\Model\ResourceModel\Widget\Instance\Collection $instanceCollection */
                $instanceCollection = $this->appCollectionFactory->create();
                $instanceCollection->addFilter('title', $row['title']);
                if ($instanceCollection->count() > 0) {
                    continue;
                }
                /** @var \Magento\Cms\Model\Block $block */
                $block = $this->cmsBlockFactory->create()->load($row['block_identifier'], 'identifier');
                if (!$block) {
                    continue;
                }
                $widgetInstance = $this->widgetFactory->create();

                $code = $row['type_code'];
                $themeId = $this->themeCollectionFactory->create()->getThemeByFullPath($row['theme_path'])->getId();
                $type = $widgetInstance->getWidgetReference('code', $code, 'type');
                $pageGroup = [];
                $group = $row['page_group'];
                $pageGroup['page_group'] = $group;
                $pageGroup[$group] = array_merge($pageGroupConfig[$group], unserialize($row['group_data']));
                if (!empty($pageGroup[$group]['entities'])) {
                    $pageGroup[$group]['entities'] = $this->getCategoryByUrlKey(
                        $pageGroup[$group]['entities']
                    )->getId();
                }

                $widgetInstance->setType($type)->setCode($code)->setThemeId($themeId);
                $widgetInstance->setTitle($row['title'])
                    ->setStoreIds([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->setWidgetParameters(['block_id' => $block->getId()])
                    ->setPageGroups([$pageGroup]);
                $widgetInstance->save();
                $this->logger->logInline('.');
            }
        }
    }

    /**
     * @param string $urlKey
     * @return \Magento\Framework\DataObject
     */
    protected function getCategoryByUrlKey($urlKey)
    {
        $category = $this->categoryFactory->create()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addUrlRewriteToResult()
            ->getFirstItem();
        return $category;
    }
}
