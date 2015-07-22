<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Review\Setup;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Review
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Review implements SetupInterface
{
    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var \Magento\Review\Model\Resource\Review\CollectionFactory
     */
    protected $reviewCollectionFactory;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Review\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * @var array
     */
    protected $productIds;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Review\Model\Rating\OptionFactory
     */
    protected $ratingOptionsFactory;

    /**
     * @var array
     */
    protected $ratings;

    /**
     * @var int
     */
    protected $ratingProductEntityId;

    /**
     * @var int
     */
    protected $reviewProductEntityId;

    /**
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Review\Model\ReviewFactory $reviewFactory
     * @param \Magento\Review\Model\Resource\Review\CollectionFactory $reviewCollectionFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param CustomerRepositoryInterface $customerAccount
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\Review\Model\Rating\OptionFactory $ratingOptionsFactory
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        \Magento\Review\Model\Resource\Review\CollectionFactory $reviewCollectionFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        CustomerRepositoryInterface $customerAccount,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\Review\Model\Rating\OptionFactory $ratingOptionsFactory,
        \Magento\SampleData\Helper\StoreManager $storeManager
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->reviewCollectionFactory = $reviewCollectionFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->ratingFactory = $ratingFactory;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
        $this->logger = $logger;
        $this->customerRepository = $customerAccount;
        $this->ratingOptionsFactory = $ratingOptionsFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing product reviews:');
        $fixtureFile = 'Review/products_reviews.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $row) {
            $storeId = [$this->storeManager->getStoreId()];
            $review = $this->prepareReview($row);
            $this->createRating($row['rating_code'], $storeId);
            $productId = $this->getProductIdBySku($row['sku']);

            if (empty($productId)) {
                continue;
            }
            /** @var \Magento\Review\Model\Resource\Review\Collection $reviewCollection */
            $reviewCollection = $this->reviewCollectionFactory->create();
            $reviewCollection->addFilter('entity_pk_value', $productId)
                ->addFilter('entity_id', $this->getReviewEntityId())
                ->addFieldToFilter('detail.title', ['eq' => $row['title']]);
            if ($reviewCollection->getSize() > 0) {
                continue;
            }

            if (!empty($row['email']) && ($this->getCustomerIdByEmail($row['email']) != null)) {
                $review->setCustomerId($this->getCustomerIdByEmail($row['email']));
            }
            $review->save();
            $this->setReviewRating($review, $row);
            $this->logger->logInline('.');
        }
    }

    /**
     * Retrieve product ID by sku
     *
     * @param string $sku
     * @return int|null
     */
    protected function getProductIdBySku($sku)
    {
        if (empty($this->productIds)) {
            foreach ($this->productCollection as $product) {
                $this->productIds[$product->getSku()] = $product->getId();
            }
        }
        if (isset($this->productIds[$sku])) {
            return $this->productIds[$sku];
        }
        return null;
    }

    /**
     * @param array $row
     * @return \Magento\Review\Model\Review
     */
    protected function prepareReview($row)
    {
        /** @var $review \Magento\Review\Model\Review */
        $review = $this->reviewFactory->create();
        $review->setEntityId(
            $review->getEntityIdByCode(\Magento\Review\Model\Review::ENTITY_PRODUCT_CODE)
        )->setEntityPkValue(
            $this->getProductIdBySku($row['sku'])
        )->setNickname(
            $row['reviewer']
        )->setTitle(
            $row['title']
        )->setDetail(
            $row['review']
        )->setStatusId(
            \Magento\Review\Model\Review::STATUS_APPROVED
        )->setStoreId(
            $this->storeManager->getStoreId()
        )->setStores(
            [$this->storeManager->getStoreId()]
        );
        return $review;
    }

    /**
     * @param string $rating
     * @return array
     */
    protected function getRating($rating)
    {
        $ratingCollection = $this->ratingFactory->create()->getResourceCollection();
        if (!$this->ratings[$rating]) {
            $this->ratings[$rating] = $ratingCollection->addFieldToFilter('rating_code', $rating)->getFirstItem();
        }
        return $this->ratings[$rating];
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @param array $row
     * @return void
     */
    protected function setReviewRating(\Magento\Review\Model\Review $review, $row)
    {
        $rating = $this->getRating($row['rating_code']);
        foreach ($rating->getOptions() as $option) {
            $optionId = $option->getOptionId();
            if (($option->getValue() == $row['rating_value']) && !empty($optionId)) {
                $rating->setReviewId($review->getId())->addOptionVote(
                    $optionId,
                    $this->getProductIdBySku($row['sku'])
                );
            }
        }
        $review->aggregate();
    }

    /**
     * @param string $ratingCode
     * @param array $stores
     * @return void
     */
    protected function createRating($ratingCode, $stores)
    {
        $stores[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $rating = $this->getRating($ratingCode);
        if (!$rating->getData()) {
            $rating->setRatingCode(
                $ratingCode
            )->setStores(
                $stores
            )->setIsActive(
                '1'
            )->setEntityId(
                $this->getRatingEntityId()
            )->save();

            /**Create rating options*/
            $options = [
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
            ];
            foreach ($options as $key => $optionCode) {
                $optionModel = $this->ratingOptionsFactory->create();
                $optionModel->setCode(
                    $optionCode
                )->setValue(
                    $key
                )->setRatingId(
                    $rating->getId()
                )->setPosition(
                    $key
                )->save();
            }
        }
    }

    /**
     * @param string $customerEmail
     * @return int|null
     */
    protected function getCustomerIdByEmail($customerEmail)
    {
        $customerData = $this->customerRepository->get($customerEmail);
        if ($customerData) {
            return $customerData->getId();
        }
        return null;
    }

    /**
     * @return int
     */
    protected function getRatingEntityId()
    {
        if (!$this->ratingProductEntityId) {
            $rating = $this->ratingFactory->create();
            $this->ratingProductEntityId = $rating->getEntityIdByCode(
                \Magento\Review\Model\Rating::ENTITY_PRODUCT_CODE
            );
        }
        return $this->ratingProductEntityId;
    }

    /**
     * @return int
     */
    protected function getReviewEntityId()
    {
        if (!$this->reviewProductEntityId) {
            /** @var $review \Magento\Review\Model\Review */
            $review = $this->reviewFactory->create();
            $this->reviewProductEntityId = $review->getEntityIdByCode(
                \Magento\Review\Model\Review::ENTITY_PRODUCT_CODE
            );
        }
        return $this->reviewProductEntityId;
    }
}
