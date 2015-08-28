<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Module\Theme;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup;
use Magento\Store\Model\Store;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Launches setup of sample data for Theme module
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallData implements Setup\InstallDataInterface
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
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @param \Magento\Theme\Model\Config $config
     * @param \Magento\Theme\Model\Resource\Theme\CollectionFactory $collectionFactory
     * @param \Magento\Framework\UrlInterface $baseUrl
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param SampleDataContext $sampleDataContext
     *
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
        SampleDataContext $sampleDataContext
    ) {
        $this->config = $config;
        $this->collectionFactory = $collectionFactory;
        $this->baseUrl = $baseUrl;
        $this->scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->configCacheType = $configCacheType;
        $this->directoryList = $directoryList;
        $this->moduleList = $moduleList;
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->assignTheme();
        $this->addHeadInclude();
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
            $fileName = $this->fixtureManager->getPath($fileName);
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
