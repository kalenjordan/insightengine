<?php

class Model_Log
{
    protected function _getLogFilePath($logFile)
    {
        return dirname(dirname(dirname(__FILE__))) . '/var/' . $logFile;
    }

    public function log($message, $logFile = 'system.log')
    {
        $logFilePath = $this->_getLogFilePath($logFile);
        file_put_contents($logFilePath, date('r') . ': ' . $message . "\r\n", FILE_APPEND);
    }
}