<?php


function tejima(sfEvent $event){
  //$message = print_r($event,true);

  $data = $event['actionInstance']->getRequest()->getPostParameters();
  error_log("DATA:".serialize($data)."\n", 3, "/tmp/aaaaaaaa");
  $data = $event['actionInstance']->getRequest()->getUri();
  error_log("DATA:".serialize($data)."\n", 3, "/tmp/aaaaaaaa");

  $commu_topic_id = 2;
  $ct = Doctrine::getTable('CommunityTopic')->find($commu_topic_id);

  //print_r($ct);
  error_log("DATA:".$ct->getCommunityId()."\n", 3, "/tmp/aaaaaaaa");
  $cm_list = Doctrine::getTable('CommunityMember')->findByCommunityId($ct->getCommunityId());
  foreach($cm_list as $cm){

    $data = $cm->getMemberId();
    error_log("DATA:member_id".$data."\n", 3, "/tmp/aaaaaaaa");
    $m = Doctrine::getTable("Member")->find($cm->getMemberId());
    $addr = $m->getConfig('pc_address');
    error_log("DATA:addr=".$addr."\n", 3, "/tmp/aaaaaaaa");
    $mail = new Zend_Mail();
    $mail->setBodyText('This is the text of the mail.');
    $mail->setFrom('tejima@tejimaya.com', 'Some Sender');
    $mail->addTo('tejima@gmail.com', 'Some Recipient');
    $mail->setSubject('TestSubject');
    $mail->send();
  }

}

$this->dispatcher->connect('op_action.post_execute_communityTopicComment_create','tejima');
