<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\CatalogRule\Setup;

use Magento\CatalogRule\Model\RuleFactory as RuleFactory;
use Magento\CatalogRule\Model\Resource\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Rule
 */
class Rule implements SetupInterface
{
    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var RuleCollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\JobFactory
     */
    protected $jobFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $websiteFactory;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param RuleFactory $ruleFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param \Magento\CatalogRule\Model\Rule\JobFactory $jobFactory
     * @param \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param Logger $logger
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        RuleFactory $ruleFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        \Magento\CatalogRule\Model\Rule\JobFactory $jobFactory,
        \Magento\Catalog\Model\Resource\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        Logger $logger
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->ruleFactory = $ruleFactory;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->jobFactory = $jobFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->groupFactory = $groupFactory;
        $this->websiteFactory = $websiteFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing catalog rules:');
        $file = 'CatalogRule/catalog_rules.csv';
        $fileName = $this->fixtureHelper->getPath($file);
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
        foreach ($csvReader as $row) {
            /** @var \Magento\CatalogRule\Model\Resource\Rule\Collection $ruleCollection */
            $ruleCollection = $this->ruleCollectionFactory->create();
            $ruleCollection->addFilter('name', $row['name']);
            if ($ruleCollection->count() > 0) {
                continue;
            }
            $row['customer_group_ids'] = $this->getGroupIds();
            $row['website_ids'] = $this->getWebsiteIds();
            $row['conditions_serialized'] = $this->convertSerializedData($row['conditions_serialized']);
            $row['actions_serialized'] = $this->convertSerializedData($row['actions_serialized']);
            $ruleModel = $this->ruleFactory->create();
            $ruleModel->loadPost($row);
            $ruleModel->save();
            $this->logger->logInline('.');
        }
        $ruleJob = $this->jobFactory->create();
        $ruleJob->applyAll();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function convertSerializedData($data)
    {
        $regexp = '/\%(.*?)\%/';
        preg_match_all($regexp, $data, $matches);
        $replacement = null;
        foreach ($matches[1] as $matchedId => $matchedItem) {
            $extractedData = array_filter(explode(",", $matchedItem));
            foreach ($extractedData as $extractedItem) {
                $separatedData = array_filter(explode('=', $extractedItem));
                if ($separatedData[0] == 'url_key') {
                    if (!$replacement) {
                        $replacement = $this->getCategoryReplacement($separatedData[1]);
                    } else {
                        $replacement .= ',' . $this->getCategoryReplacement($separatedData[1]);
                    }
                }
            }
            if (!empty($replacement)) {
                $data = preg_replace('/' . $matches[0][$matchedId] . '/', serialize($replacement), $data);
            }
        }
        return $data;
    }

    /**
     * @param string $urlKey
     * @return mixed|null
     */
    protected function getCategoryReplacement($urlKey)
    {
        $categoryCollection = $this->categoryCollectionFactory->create();
        $category = $categoryCollection->addAttributeToFilter('url_key', $urlKey)->getFirstItem();
        $categoryId = null;
        if (!empty($category)) {
            $categoryId = $category->getId();
        }
        return $categoryId;
    }

    /**
     * @return array
     */
    public function getGroupIds()
    {
        $groupsIds = [];
        $collection = $this->groupFactory->create()->getCollection();
        foreach ($collection as $group) {
            $groupsIds[] = $group->getId();
        }
        return $groupsIds;
    }

    /**
     * @return array
     */
    public function getWebsiteIds()
    {
        $websiteIds = [];
        $collection = $this->websiteFactory->create()->getCollection();
        foreach ($collection as $website) {
            $websiteIds[] = $website->getId();
        }
        return $websiteIds;
    }
}
