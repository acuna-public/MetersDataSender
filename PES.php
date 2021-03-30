<?php
  
  namespace MetersDataSender;
  
  class PES extends \MetersDataSender {
    
    protected $url = 'https://ikus.pesc.ru';
    
    protected function query ($uri, $v = 3, $data = []): array {
      
      $curl = new \Curl ();
      
      $data['method'] = ($data ? \Curl::POST : \Curl::GET);
      $data['url'] = $this->url.'/application/v'.$v.'/'.$uri;
      $data['headers'] = ['Authorization' => $curl->auth ($this->params['token'])];
      
      $curl->setData ($data);
      
      return $curl->process ()[0]->getJSON ();
      
    }
    
    public function getName () {
      return 'Петроэлектросбыт';
    }
    
    public function getUserData (): array {
      
      $data = $this->query ('profile');
      
      return ['first_name' => $data['firstName'], 'last_name' => $data['lastName'], 'middle_name' => $data['middleName'], 'email' => $data['email']];
      
    }
    
    public function getGroups (): array {
      
      $output = [];
      
      foreach ($this->query ('groups') as $group)
      $output[] = ['id' => $group['id'], 'name' => $group['name']];
      
      return $output;
      
    }
    
    public function getAccounts ($group): array {
      
      $output = [];
      
      foreach ($this->query ('groups/'.$group.'/accounts', 5) as $account)
      $output[] = ['id' => $account['accountId'], 'name' => $account['serviceName'], 'subscriber_id' => $account['accountDisplayKey'][0]['fieldValue']];
      
      return $output;
      
    }
    
    public function getData ($account): array {
      
      $data = $this->query ('accounts/'.$account.'/data', 4);
      
      $output = ['balance' => $data['balanceDetails']['balance'], 'meters' => []];
      
      foreach ($data['indicationInfo']['subServices'] as $service)
        $output['meters'][] = ['name' => $service['subserviceName'], 'value' => $service['value'], 'id' => $service['subserviceId'], 'parameters' => $service['dutyParameters'], 'meter_id' => $service['meterId'], 'meter_number' => $service['meterNumber'], 'scale' => $service['scale']];
      
      return $output;
      
    }
    
    public function sendMeters ($account_id, $meters, $group_id = 0): array {
      
      $data = [
        
        'accountId' => $account_id,
        'newIndications' => [],
        
      ];
      
      foreach ($meters as $meter) {
        
        $data['newIndications'][] = [
          
          'dutyParameters' => [],
          'meterId' => $meter['meter_id'],
          'meterNumber' => $meter['meter_number'],
          'scale' => $meter['scale'],
          'subserviceId' => $meter['id'],
          'value' => $meter['value'],
          
        ];
        
      }
      
      $data = [
        
        'post_fields' => $data,
        'data_type' => 'json',
        'referer' => $this->url.'/groups/'.$group_id.'/indications',
        
      ];
      
      //return $data;
      
      return $this->query ('accounts/indications/declare', 4, $data);
      
    }
    
    public function getUsualData ($account, $meter): array {
      return $this->query ('accounts/'.$account.'/'.$meter.'/consumption/usual');
    }
    
  }