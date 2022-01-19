<?php
  
  namespace MetersDataSender;
  
  class Mir extends \MetersDataSender {
    
    public
      $url = 'https://lk.ucmir.ru/desk/widget',
      $main = 'https://lk.ucmir.ru/desk/view';
    
    protected function query ($url, $data = []) {
      
      $data['url'] = $url;
      $data['cookies'] = ['text' => $this->cookies];
      
      $this->curl->setData ($data);
      
      return $this->curl->process ()[0]->getHTML ();
      
    }
    
    public function getName () {
      return 'УК "Мир"';
    }
    
    public function getUserData (): array {
      return [];
    }
    
    public function getData ($account = 0): array {
      
      $html = $this->query ($this->main);
      
      $data = ['csrf' => $html->find ('[name=csrf-token]', 0)->content, 'meters' => []];
      
      $html = $this->query ($this->url.'/meter');
      
      foreach ($html->find ('div[class=block -collapsed]') as $div) {
        
        $value = explode (':', $div->find ('.block-content p', 2)->text);
        $value = preg_split ('~\s+от\s+~', $value[1]);
        
        $data['meters'][] = ['name' => trim ($div->find ('.block-header p', 0)->text), 'value' => trim ($value[0]), 'url' => $div->find ('form', 0)->action];
      
      }
      
      return $data;
      
    }
    
    public function sendMeters ($account, $meters, $group_id = 0) {
      
      foreach ($meters['meters'] as $meter) {
        
        $data = [
          
          'method' => \Curl::POST,
          'url' => $meter['url'],
          'cookies' => ['text' => $this->cookies],
          'referer' => $this->main,
          
          'post_fields' => [
            'value' => $meter['value'],
          ],
          
          'headers' => ['X-CSRF-TOKEN' => $meters['csrf']],
          
        ];
        
        //print_r ($data);
        
        $this->curl->setData ($data);
        
      }
      
      //return $this->curl->process ()[0]->getContent ();
      
    }
    
  }