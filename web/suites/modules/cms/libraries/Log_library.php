<?php

/**
 * Created by PhpStorm.
 * User: dev_001
 * Date: 18/4/13
 * Time: 下午5:10
 */
class Log_library
{
    private $logPath = '';

    function __construct()
    {
        $this->logPath = dirname(dirname(__FILE__))."/log/";
        if(!file_exists($this->logPath)){
            mkdir($this->logPath);
        }
    }

    /**
     * @param $logFile
     * @param $log
     * 写入日志
     */
    public function writeLog($fileId,$log){
        file_put_contents($this->logPath.$fileId,$log."\n",FILE_APPEND);

    }


    public function readLog($fileId){
        if(!file_exists($this->logPath.$fileId)){
            throw new Exception('file not exists');
        }

        return file_get_contents($this->logPath.$fileId);
    }

}