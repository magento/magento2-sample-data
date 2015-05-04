<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Helper;

class Fixture
{
    /**
     * @todo replace to real path to fixtures
     *
     * @param string $subPath
     * @return string
     */
    public function getPath($subPath)
    {
        return realpath(__DIR__ . '/../fixtures/' . ltrim($subPath, '/'));
    }

    /**
     * @param string $subPath
     * @return array
     */
    public function getDirectoryFiles($subPath)
    {
        $result = [];
        $dir = $this->getPath($subPath);
        if (!is_dir($dir)) {
            return $result;
        }
        $files = scandir($dir);
        foreach ($files as $file) {
            if (is_file($dir . '/' . $file)) {
                $result[] = $subPath . '/' . $file;
            }
        }

        return $result;
    }
}
