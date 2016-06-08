<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\DownloadableSampleData\Model\Product;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem;

/**
 * Class Converter
 */
class Converter extends \Magento\CatalogSampleData\Model\Product\Converter
{
    /**
     * @var \Magento\Downloadable\Api\Data\File\ContentInterfaceFactory
     */
    private $fileContentFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * Get downloadable data from array
     *
     * @param array $row
     * @param array $downloadableData
     * @return array
     */
    public function getDownloadableData($row, $downloadableData = [])
    {
        $separatedData = $this->groupDownloadableData($row);
        $formattedData = $this->getFormattedData($separatedData);
        foreach (array_keys($formattedData) as $dataType) {
            $downloadableData[$dataType][] = $formattedData[$dataType];
        }

        return $downloadableData;
    }

    /**
     * Group downloadable data by link and sample array keys.
     *
     * @param array $downloadableData
     * @return array
     */
    public function groupDownloadableData($downloadableData)
    {
        $groupedData = [];
        foreach ($downloadableData as $dataKey => $dataValue) {
            if (!empty($dataValue)) {
                if ((preg_match('/^(link_item)/', $dataKey, $matches)) && is_array($matches)) {
                    $groupedData['link'][$dataKey] = $dataValue;
                }
            }
            unset($dataKey);
            unset($dataValue);
        }

        return $groupedData;
    }

    /**
     * Will format data corresponding to the product sample data array values.
     *
     * @param array $groupedData
     * @return array
     */
    public function getFormattedData($groupedData)
    {
        $formattedData = [];
        foreach (array_keys($groupedData) as $dataType) {
            if ($dataType == 'link') {
                $formattedData['link'] = $this->formatDownloadableLinkData($groupedData['link']);
            }
        }

        return $formattedData;
    }

    /**
     * Format downloadable link data
     *
     * @param array $linkData
     * @return array
     */
    public function formatDownloadableLinkData($linkData)
    {
        $linkItems = [
            'link_item_title',
            'link_item_price',
            'link_item_file',
        ];
        foreach ($linkItems as $csvRow) {
            $linkData[$csvRow] = isset($linkData[$csvRow]) ? $linkData[$csvRow] : '';
        }
        $directory = $this->getFilesystem()->getDirectoryRead(DirectoryList::MEDIA);
        $linkPath = $directory->getAbsolutePath('downloadable/files/links' . $linkData['link_item_file']);
        $data = base64_encode(file_get_contents($linkPath));
        $content = $this->getFileContent()->setFileData($data)
            ->setName('luma_background_-_model_against_fence_4_sec_.mp4');
        $link = [
            'is_delete' => '',
            'link_id' => '0',
            'title' => $linkData['link_item_title'],
            'price' => $linkData['link_item_price'],
            'number_of_downloads' => '0',
            'is_shareable' => '2',
            'type' => 'file',
            'file' => json_encode([['file' => $linkData['link_item_file'], 'status' => 'old']]),
            'sort_order' => '',
            'link_file_content' => $content
        ];

        return $link;
    }

    /**
     * Returns information about product's samples
     * @return array
     */
    public function getSamplesInfo()
    {
        $directory = $this->getFilesystem()->getDirectoryRead(DirectoryList::MEDIA);
        $linkPath = $directory->getAbsolutePath(
            'downloadable/files/samples/l/u/luma_background_-_model_against_fence_4_sec_.mp4'
        );
        $data = base64_encode(file_get_contents($linkPath));
        $content = $this->getFileContent()->setFileData($data)
            ->setName('luma_background_-_model_against_fence_4_sec_.mp4');
        $sample = [
            'is_delete' => '',
            'sample_id' => '0',
            'file' => json_encode([[
                'file' => '/l/u/luma_background_-_model_against_fence_4_sec_.mp4',
                'status' => 'old',
            ]]),
            'type' => 'file',
            'sort_order' => '',
            'sample_file_content' => $content
        ];

        $samples = [];
        for ($i = 1; $i <= 3; $i++) {
            $sample['title'] = 'Trailer #' . $i;
            $samples[] = $sample;
        }

        return $samples;
    }

    /**
     * @return \Magento\Downloadable\Api\Data\File\ContentInterface
     * @deprecated
     */
    private function getFileContent()
    {
        if (!$this->fileContentFactory) {
            $this->fileContentFactory = ObjectManager::getInstance()->create(
                \Magento\Downloadable\Api\Data\File\ContentInterfaceFactory::class
            );
        }
        return $this->fileContentFactory->create();
    }

    /**
     * @return Filesystem
     * @deprecated
     */
    private function getFilesystem()
    {
        if (!$this->filesystem) {
            $this->filesystem = ObjectManager::getInstance()->create(
                Filesystem::class
            );
        }
        return $this->filesystem;
    }
}
