<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Customer\Setup;

use Magento\Customer\Api\Data\RegionInterface;
use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Customer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Customer implements SetupInterface
{
    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Api\Data\RegionInterfaceFactory
     */
    protected $regionFactory;

    /** @var \Magento\Customer\Api\AccountManagementInterface */
    protected $accountManagement;

    /**
     * @var array $customerDataProfile
     */
    protected $customerDataProfile;

    /**
     * @var array $customerDataAddress
     */
    protected $customerDataAddress;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory
     * @param \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param array $fixtures
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressFactory,
        \Magento\Customer\Api\Data\RegionInterfaceFactory $regionFactory,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        $fixtures = ['Customer/customer_profile.csv']
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->countryFactory = $countryFactory;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->regionFactory = $regionFactory;
        $this->accountManagement = $accountManagement;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->fixtures = $fixtures;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing customers:');
        foreach ($this->fixtures as $file) {
            /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                // Collect customer profile and addresses data
                $customerData['profile'] = $this->convertRowData($row, $this->getDefaultCustomerProfile());
                $customerData['address'] = $this->convertRowData($row, $this->getDefaultCustomerAddress());
                $customerData['address']['region_id'] = $this->getRegionId($customerData['address']);

                $address = $customerData['address'];
                $regionData = [
                    RegionInterface::REGION_ID => $address['region_id'],
                    RegionInterface::REGION => !empty($address['region']) ? $address['region'] : null,
                    RegionInterface::REGION_CODE => !empty($address['region_code']) ? $address['region_code'] : null,
                ];
                $region = $this->regionFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $region,
                    $regionData,
                    '\Magento\Customer\Api\Data\RegionInterface'
                );

                $addresses = $this->addressFactory->create();
                unset($customerData['address']['region']);
                $this->dataObjectHelper->populateWithArray(
                    $addresses,
                    $customerData['address'],
                    '\Magento\Customer\Api\Data\AddressInterface'
                );
                $addresses->setRegion($region)
                    ->setIsDefaultBilling(true)
                    ->setIsDefaultShipping(true);

                $customer = $this->customerFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $customer,
                    $customerData['profile'],
                    '\Magento\Customer\Api\Data\CustomerInterface'
                );
                $customer->setAddresses([$addresses]);
                $this->accountManagement->createAccount($customer, $row['password']);
                $this->logger->logInline('.');
            }
        }
    }

    /**
     * @return array
     */
    protected function getDefaultCustomerProfile()
    {
        if (!$this->customerDataProfile) {
            $this->customerDataProfile = [
                'website_id' => $this->storeManager->getWebsiteId(),
                'group_id' => $this->storeManager->getGroupId(),
                'disable_auto_group_change' => '0',
                'prefix',
                'firstname' => '',
                'middlename' => '',
                'lastname' => '',
                'suffix' => '',
                'email' => '',
                'dob' => '',
                'taxvat' => '',
                'gender' => '',
                'confirmation' => false,
                'sendemail' => false,
            ];
        }
        return $this->customerDataProfile;
    }

    /**
     * @return array
     */
    protected function getDefaultCustomerAddress()
    {
        if (!$this->customerDataAddress) {
            $this->customerDataAddress = [
                'prefix' => '',
                'firstname' => '',
                'middlename' => '',
                'lastname' => '',
                'suffix' => '',
                'company' => '',
                'street' => [
                    0 => '',
                    1 => '',
                ],
                'city' => '',
                'country_id' => '',
                'region' => '',
                'postcode' => '',
                'telephone' => '',
                'fax' => '',
                'vat_id' => '',
                'default_billing' => true,
                'default_shipping' => true,
            ];
        }
        return $this->customerDataAddress;
    }

    /**
     * @param array $row
     * @param array $data
     * @return array $data
     */
    protected function convertRowData($row, $data)
    {
        foreach ($row as $field => $value) {
            if (isset($data[$field])) {
                if ($field == 'street') {
                    $data[$field] = unserialize($value);
                    continue;
                }
                if ($field == 'password') {
                    continue;
                }
                $data[$field] = $value;
            }
        }
        return $data;
    }

    /**
     * @param array $address
     * @return mixed
     */
    protected function getRegionId($address)
    {
        $country = $this->countryFactory->create()->loadByCode($address['country_id']);
        return $country->getRegionCollection()->addFieldToFilter('name', $address['region'])->getFirstItem()->getId();
    }
}
