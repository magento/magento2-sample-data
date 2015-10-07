<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CmsSampleData\Model;

use Magento\Framework\Setup;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Launches setup of sample data for Theme module
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class HeadStyle
{
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
     * @param \Magento\Framework\UrlInterface $baseUrl
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\UrlInterface $baseUrl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
        ) {
            $this->baseUrl = $baseUrl;
            $this->scopeConfig = $scopeConfig;
            $this->configWriter = $configWriter;
            $this->configCacheType = $configCacheType;
            $this->directoryList = $directoryList;
        }

    public function add($contentFile, $cssFile)
    {
        $styleContent = preg_replace('/^\/\*[\s\S]+\*\//', '', file_get_contents($contentFile));
        if (empty($styleContent)) {
            return;
        }

        $mediaDir = $this->directoryList->getPath(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        file_put_contents("{$mediaDir}/{$cssFile}", $styleContent, FILE_APPEND);

        $linkText = sprintf(
            '<link  rel="stylesheet" type="text/css"  media="all" href="%s%s" />',
            $this->baseUrl->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]),
            $cssFile
        );

        $miscScriptsNode = 'design/head/includes';
        $miscScripts = $this->scopeConfig->getValue($miscScriptsNode);
        if (!$miscScripts || strpos($miscScripts, $linkText) === false) {
            $this->configWriter->save($miscScriptsNode, $miscScripts . $linkText);
            $this->configCacheType->clean();
        }
    }
}