<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData\Module\Theme;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\Store;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\Logger;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Theme module
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Setup implements SetupInterface
{
    /**
     * @var \Magento\Theme\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Theme\Model\Resource\Theme\CollectionFactory
     */
    private $collectionFactory;

    /**
     * Url configuration
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $baseUrl;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    private $directoryList;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Magento\Theme\Model\Config $config
     * @param \Magento\Theme\Model\Resource\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Framework\UrlInterface $baseUrl
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param FixtureHelper $fixtureHelper
     * @param Logger $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Theme\Model\Config $config,
        \Magento\Theme\Model\Resource\Theme\CollectionFactory $collectionFactory,
        \Magento\Framework\UrlInterface $baseUrl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        FixtureHelper $fixtureHelper,
        Logger $logger
    ) {
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;
        $this->baseUrl = $baseUrl;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->configCacheType = $configCacheType;
        $this->directoryList = $directoryList;
        $this->moduleList = $moduleList;
        $this->fixtureHelper = $fixtureHelper;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing theme:');
        $this->assignTheme();
        $this->addHeadInclude();
        $this->logger->log('.');
    }

    /**
     * Assign Theme
     *
     * @return void
     */
    protected function assignTheme()
    {
        $themes = $this->collectionFactory->create()->loadRegisteredThemes();
        /** @var \Magento\Theme\Model\Theme $theme */
        foreach ($themes as $theme) {
            if ($theme->getCode() == 'Magento/luma') {
                $this->config->assignToStore(
                    $theme,
                    [Store::DEFAULT_STORE_ID],
                    ScopeConfigInterface::SCOPE_TYPE_DEFAULT
                );
            }
        }
    }

    /**
     * Add Link to Head
     *
     * @return void
     */
    protected function addHeadInclude()
    {
        $styleContent = '';
        foreach ($this->moduleList->getNames() as $moduleName) {
            $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/styles.css';
            $fileName = $this->fixtureHelper->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            $style = file_get_contents($fileName);
            $styleContent .= preg_replace('/^\/\*[\s\S]+\*\//', '', $style);
        }
        if (empty($styleContent)) {
            return;
        }
        $mediaDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        file_put_contents("$mediaDir/styles.css", $styleContent);
        $linkTemplate = '<link  rel="stylesheet" type="text/css"  media="all" href="%sstyles.css" />';
        $baseUrl = $this->baseUrl->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]);
        $linkText = sprintf($linkTemplate, $baseUrl);

        $miscScriptsNode = 'design/head/includes';
        $miscScripts = $this->scopeConfig->getValue($miscScriptsNode);
        if (!$miscScripts || strpos($miscScripts, $linkText) === false) {
            $this->configWriter->save($miscScriptsNode, $miscScripts . $linkText);
            $this->configCacheType->clean();
        }
    }
}
