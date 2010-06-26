<?php 
class MLTask extends sfBaseTask
{
  protected function configure()
  {
    set_time_limit(120);
    mb_language("Japanese");
    mb_internal_encoding("utf-8");

    $this->namespace = 'tjm';
    $this->name      = 'ML';
    $this->aliases   = array('tjm-ML');
    $this->breafDescription = '';
  }
  protected function execute($arguments = array(),$options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    $this->ml();
  }
  private function ml(){

    sfConfig::set('sf_test', true); //これを置かないと止まる ProjectConfigurationで。
    echo "---------------------------->processPOP3() @pne.jp \n";
    try{
      $mail = new Zend_Mail_Storage_Pop3(
         array('host' => Doctrine::getTable('SnsConfig')->get('oppop3_pop3_host'),
              'user' => Doctrine::getTable('SnsConfig')->get('oppop3_pop3_user'),
              'password' => Doctrine::getTable('SnsConfig')->get('oppop3_pop3_pass'),
              'ssl' => 'SSL',
              'port' => 995)
         );
      echo $mail->countMessages() . " messages found(from POP3 Server)\n";
      $count = $mail->countMessages();
      if($count == 0){
        return;
      }
      mb_internal_encoding('UTF-8');
      $raw_data = $mail->getRawHeader(1) . "\r\n\r\n" .  $mail->getRawContent(1);
      //$opMessage = new opMailMessage(array('raw' =>$raw_data));
      echo "--------------------------opMessage.content\n";

      $message = new opMailMessage(array('raw' => $raw_data));
      $subject = $message->getHeader('Subject');
      $body = $message->getContent();
      $to = $message->getHeader('To');
      
      preg_match('/\+c(.*?)@/', $to, $matches);
      if(!$matches){
        return;
      }else{
        print_r($matches);
        $community_id = $matches[1];
      }
      
      print_r("Subject: " . $subject);
      print_r("Body: " . $body);
      //print_r($message);
     //community_id  name
      $result = Doctrine::getTable('CommunityTopic')->findOneByCommunityIdAndName($community_id,'ML');
      print_r($result->id);
      $obj = new CommunityTopicComment();
      $obj->setCommunityTopicId($result->id);
      $obj->setMemberId(1);
      $obj->setBody($body);
      $obj->save();
      
      $cm_list = Doctrine::getTable('CommunityMember')->findByCommunityId($community_id);
      $addr_list = array();
      foreach($cm_list as $cm){
        $m = Doctrine::getTable('Member')->find($cm->getMemberId());
        $addr = $m->getConfig('pc_address');
        $addr_list[] = $addr;
      }
      print_r($addr_list);

      $from = sfConfig::get('op_prefix') . "+c" . $community_id . "@" . sfConfig::get('op_mail_domain');

//      $body = $c['body'] . $member->getName() . "karano toukou.";
      $mail = new opMailSend();
      $mail->subject = 'subject';
      $mail->body = $body;

      foreach($addr_list as $addr){
        $mail->send($addr,$from);
      }
     $mail->removeMessage(1);
    }catch(Exception $e){
       echo $e->getMessage();
    }
  }
}
