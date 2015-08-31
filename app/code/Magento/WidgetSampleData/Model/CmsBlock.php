<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\WidgetSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Launches setup of sample data for Widget module
 */
class CmsBlock
{
    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Widget\Model\Widget\InstanceFactory
     */
    protected $widgetFactory;

    /**
     * @var \Magento\Theme\Model\Resource\Theme\CollectionFactory
     */
    protected $themeCollectionFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $cmsBlockFactory;

    /**
     * @var \Magento\Widget\Model\Resource\Widget\Instance\CollectionFactory
     */
    protected $appCollectionFactory;

    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory
     * @param \Magento\Theme\Model\Resource\Theme\CollectionFactory $themeCollectionFactory
     * @param \Magento\Cms\Model\BlockFactory $cmsBlockFactory
     * @param \Magento\Widget\Model\Resource\Widget\Instance\CollectionFactory $appCollectionFactory
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Magento\Theme\Model\Resource\Theme\CollectionFactory $themeCollectionFactory,
        \Magento\Cms\Model\BlockFactory $cmsBlockFactory,
        \Magento\Widget\Model\Resource\Widget\Instance\CollectionFactory $appCollectionFactory,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryFactory
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $csvReader;
        $this->widgetFactory = $widgetFactory;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->cmsBlockFactory = $cmsBlockFactory;
        $this->appCollectionFactory = $appCollectionFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(array $fixtures)
    {
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

        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getPath($fileName);
            $csvReader = $this->csvReader->getData($fileName);
            foreach ($csvReader as $row) {
                /** @var \Magento\Widget\Model\Resource\Widget\Instance\Collection $instanceCollection */
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
