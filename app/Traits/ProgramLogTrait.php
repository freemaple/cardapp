<?php
namespace App\Traits;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

trait ProgramLogTrait{
	private $log_prefix;
	public  $log_path;
	public 	$log_fname;

	private $logger;

	//初始化日志保存路径
	public function initLogPath($path_prefix = 'joblog', $logger_type = 'queue'){
		$this->log_prefix = storage_path("logs/".$path_prefix);

		$this->log_path = preg_replace("/[\s]/", '', $this->log_path);
		$this->log_prefix = preg_replace("/[\s]/", '', $this->log_prefix);

		//创建目录
		$log_path = $this->log_prefix."/".$this->log_path."/".date('Y-m')."/";
		
		if(empty($this->log_fname) || !isset($this->log_fname)){
			$this->log_fname = $log_path.$this->log_path.'-'.date('d').".log";
		}else{
			$this->log_fname = $log_path.$this->log_fname.'-'.date('d').".log";
		}

		$this->logger = new Logger($logger_type);
        $this->logger->pushHandler(
            new StreamHandler($this->log_fname, Logger::INFO)
        );
        
	}
	//记录日志
	public function saveLog($msg)
	{
		try{
			$this->logger->addInfo($msg);
		}catch(\Exception $e){}
	}
}