<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Helper;

class State
{
    const STATE_NOT_STARTED = 'not_started';

    const STATE_STARTED = 'started';

    const STATE_FINISHED = 'finished';

    /**
     * @var array
     */
    protected $allowedStates = [
        self::STATE_STARTED,
        self::STATE_FINISHED
    ];

    /**
     * @var string
     */
    protected $fileName = 'sample-data-state.flag';

    /**
     * Get file resource to write sample data installation state
     *
     * @param string $mode
     * @return resource|false
     */
    protected function getStream($mode = 'r')
    {
        $filePath = BP . '/var/' . $this->fileName;
        $stream = @fopen($filePath, $mode);
        return $stream;
    }

    /**
     * Closing file stream
     *
     * @param resource|false $handle
     * @return void
     */
    protected function closeStream($handle)
    {
        if ($handle) {
            fclose($handle);
        }
    }

    /**
     * Verify if correct state provided
     *
     * @param string $state
     * @return bool
     */
    protected function isStateCorrect($state)
    {
        return in_array($state, $this->allowedStates);
    }

    /**
     * State getter
     *
     * @return string
     */
    public function getState()
    {
        $defaultState = self::STATE_NOT_STARTED;
        $stream = $this->getStream('r');
        if (!$stream) {
            return $defaultState;
        }
        $state = trim(fread($stream, 400));
        $this->closeStream($stream);
        if ($this->isStateCorrect($state)) {
            return $state;
        }
        return $defaultState;
    }

    /**
     * @param string $state
     * @return $this
     * @throws \Exception
     */
    public function setState($state)
    {
        if ($this->isStateCorrect($state)) {
            $stream = $this->getStream('w');
            if ($stream === false) {
                throw new \Exception(
                    'Please, ensure that file var/state.lock inside Sample data directory exists and is writable'
                );
            }
            fwrite($stream, $state);
            $this->closeStream($stream);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function start()
    {
        return $this->setState(self::STATE_STARTED);
    }

    /**
     * @return $this
     */
    public function finish()
    {
        return $this->setState(self::STATE_FINISHED);
    }
}
