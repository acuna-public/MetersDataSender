<?php
  
  abstract class MetersDataSender extends Mash\Adapter {
    
    public $cookies = '';
    
    protected $mash, $curl;
    
    function __construct (Mash $mash, $config = []) {
      
      parent::__construct ($config);
      
      $mash->init ();
      
      $this->curl = new \Curl ();
      
    }
    
    public abstract function getUserData (): array;
    public abstract function getData ($account = 0): array;
    public abstract function sendData ($meters, $account = 0, $group_id = 0);
    
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