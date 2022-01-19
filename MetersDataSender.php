<?php
  
  require __DIR__.'/../Mash/Adapter.php';
  
  abstract class MetersDataSender extends Mash\Adapter {
    
    public $cookies = '';
    
    protected $curl;
    
    function __construct ($config = []) {
      
      parent::__construct ($config);
      
      $this->curl = new \Curl ();
      
    }
    
    public abstract function getUserData (): array;
    public abstract function getData ($account = 0): array;
    public abstract function sendMeters ($account, $meters, $group_id = 0);
    
    public function getGroups (): array {
      return [];
    }
    
    public function getAccounts ($group): array {
      return [];
    }
    
    public function getUsualData ($account, $meter): array {
      return [];
    }
    
  }