<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Model;

use Magento\Framework\Setup\LoggerInterface;

class TestLogger implements LoggerInterface
{
    /**
     * Creates a test logger
     *
     * @return Logger
     */
    public static function factory()
    {
        $logger = new Logger;
        $logger->setSubject(new TestLogger);
        return $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        $this->writeLn($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        $this->writeLn($e);
    }

    /**
     * {@inheritdoc}
     */
    public function logInline($message)
    {
        echo $message;
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        $this->writeLn($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        $this->writeLn($message);
    }

    /**
     * Write line
     *
     * @param string $message
     */
    private function writeLn($message)
    {
        echo $message . PHP_EOL;
    }
}
