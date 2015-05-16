<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData;

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
     * @return LoggerInterface
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function log($message)
    {
        return $this->getSubject()->log($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logInline($message)
    {
        return $this->getSubject()->logInline($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logError(\Exception $e)
    {
        return $this->getSubject()->logError($e);
    }

    /**
     * {@inheritdoc}
     */
    public function logMeta($message)
    {
        return $this->getSubject()->logMeta($message);
    }

    /**
     * {@inheritdoc}
     */
    public function logSuccess($message)
    {
        return $this->getSubject()->logSuccess($message);
    }
}
