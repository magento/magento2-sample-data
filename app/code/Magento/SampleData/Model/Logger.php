<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Model;

use Magento\Setup\Model\LoggerInterface;

/**
 * A dirty proxy for Setup application logger
 */
class Logger implements LoggerInterface
{
    /**
     * Proxy subject
     *
     * @var LoggerInterface
     */
    private $subject;

    /**
     * Setter for the proxy subject
     *
     * Known issue: a proper way to implement proxy is to inject subject through constructor.
     * This implementation is a workaround of integration of Magento Setup application and framework object manager
     *
     * @param LoggerInterface $subject
     * @return void
     */
    public function setSubject(LoggerInterface $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        return $this->subject->log($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logInline($message)
    {
        return $this->subject->logInline($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        return $this->subject->logError($e);
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        return $this->subject->logMeta($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        return $this->subject->logSuccess($message);
    }
}
