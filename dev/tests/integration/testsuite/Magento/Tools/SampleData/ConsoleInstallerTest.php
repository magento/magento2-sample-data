<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData;

/**
 * Class InstallTest
 */
class InstallerTest extends \PHPUnit_Framework_TestCase
{
    public function testRun()
    {
        $cmd = 'php -f '
            . $this->getPathToSource()
            . '/dev/tools/Magento/Tools/SampleData/install.php --'
            . ' --admin_user=' . \Magento\TestFramework\Bootstrap::ADMIN_NAME
            . ' --modules=Magento_Null';
        exec($cmd, $output, $returnCode);
        $this->assertEquals(0, $returnCode);
    }

    private function getPathToSource()
    {
        return \Magento\Framework\App\Utility\Files::init()->getPathToSource();
    }
}
