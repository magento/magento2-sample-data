<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRuleSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\TargetRule\Model\RuleFactory as RuleFactory;

/**
 * Class Setup
 * Installation of related products rules
 */
class Rule implements SetupInterface
{
    /**
     * @var  SampleDataContext
     */
    protected $sampleDataContext;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryManagementInterface
     */
    protected $categoryReadService;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\File\Csv $csvReader
     * @param RuleFactory $ruleFactory
     * @param \Magento\Catalog\Api\CategoryManagementInterface $categoryReadService
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\File\Csv $csvReader,
        RuleFactory $ruleFactory,
        \Magento\Catalog\Api\CategoryManagementInterface $categoryReadService
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReaderFactory = $csvReader;
        $this->ruleFactory = $ruleFactory;
        $this->categoryReadService = $categoryReadService;
    }

    /**
     * @param array $categoryPath
     * @param string $ruleType
     * @return array|null
     */
    protected function getConditionFromCategory($categoryPath, $ruleType = 'Rule')
    {
        $categoryId = null;
        $tree = $this->categoryReadService->getTree();
        foreach ($categoryPath as $categoryName) {
            $categoryId = null;
            foreach ($tree->getChildrenData() as $child) {
                if ($child->getName() == $categoryName) {
                    $tree = $child;
                    /** @var \Magento\Catalog\Api\Data\CategoryTreeInterface $child */
                    $categoryId = $child->getId();
                    break;
                }
            }
        }
        if (!$categoryId) {
            return null;
        }

        $types = [
            'Rule' => 'Magento\TargetRule\Model\Rule\Condition\Product\Attributes',
            'Actions' => 'Magento\TargetRule\Model\Actions\Condition\Product\Attributes',
        ];
        if (empty($types[$ruleType])) {
            return null;
        }
        return [
          'type' => $types[$ruleType],
          'attribute' => 'category_ids',
          'operator' => '==',
          'value' => $categoryId
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $fixtures)
    {
        foreach ($fixtures as $linkTypeId => $fileName) {
            $fileName = $this->fixtureManager->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            $csvReader = $this->csvReader->getData($fileName);
            foreach ($csvReader as $row) {
                $rule = $this->ruleFactory->create();
                if ($rule->getResourceCollection()->addFilter('name', $row['name'])->getSize() > 0) {
                    continue;
                }

                $sourceCategory = $this->getConditionFromCategory(
                    array_filter(explode("\n", $row['source_category'])),
                    'Rule'
                );
                $targetCategory = $this->getConditionFromCategory(
                    array_filter(explode("\n", $row['target_category'])),
                    'Actions'
                );
                if (!$sourceCategory || !$targetCategory) {
                    continue;
                }
                $targetCategory['value_type'] = TargetRuleActionAttributes::VALUE_TYPE_CONSTANT;

                $ruleConditions = $this->createConditions($sourceCategory, $targetCategory);

                $rule->setName($row['name'])
                    ->setApplyTo($linkTypeId)
                    ->setIsActive(1)
                    ->setSortOrder(0)
                    ->setPositionsLimit(empty($row['limit']) ? 0 : $row['limit']);
                $rule->loadPost($ruleConditions);
                $rule->save();
            }
        }
    }

    /**
     * @param string $sourceCategory
     * @param string $targetCategory
     * @return array
     */
    protected function createConditions($sourceCategory, $targetCategory)
    {
        $combineCondition = [
            'aggregator' => 'all',
            'value' => '1',
            'new_child' => '',
        ];
        $ruleConditions = [
            'conditions' => [
                1 => $combineCondition + ['type' => 'Magento\TargetRule\Model\Rule\Condition\Combine'],
                '1--1' => $sourceCategory,
            ],
            'actions' => [
                1 => $combineCondition + ['type' => 'Magento\TargetRule\Model\Actions\Condition\Combine'],
                '1--1' => $targetCategory,
            ],
        ];
        if (!empty($row['conditions'])) {
            $index = 2;
            foreach (array_filter(explode("\n", $row['conditions'])) as $condition) {
                $ruleConditions['actions']['1--' . $index] = unserialize($condition);
                $index++;
            }
        }
        return $ruleConditions;
    }
}
