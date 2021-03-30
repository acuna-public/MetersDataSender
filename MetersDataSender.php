<?php
  
  require __DIR__.'/../Mash/Mash.php';
  require __DIR__.'/../Mash/wrappers/Adapter.php';
  
  abstract class MetersDataSender extends Mash\Adapter {
    
    function __construct (array $config) {
      $this->params = $config;
    }
    
    public function getRootDir () {
      return __DIR__;
    }
    
    public abstract function getGroups (): array;
    public abstract function getAccounts ($group): array;
    public abstract function getData ($account): array;
    public abstract function sendMeters ($account_id, $meters, $group_id = 0): array;
    
    public function getUsualData ($account, $meter): array {
      return [];
    }
    
  }