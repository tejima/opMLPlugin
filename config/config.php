<?php


function tejima(sfEvent $event){
  //$message = print_r($event,true);

  $req = $event['actionInstance']->getRequest();

  //$data = $req->getParameter('id');
  //print_r($data);
  //exit;


  $ct = Doctrine::getTable('CommunityTopic')->find($req->getParameter('id'));

  $member = $event['actionInstance']->getUser()->getMember();
  

  //print_r($ct);
  error_log("DATA:".$ct->getCommunityId()."\n", 3, "/tmp/aaaaaaaa");
  $cm_list = Doctrine::getTable('CommunityMember')->findByCommunityId($ct->getCommunityId());
  foreach($cm_list as $cm){

    $data = $cm->getMemberId();
    error_log("DATA:member_id".$data."\n", 3, "/tmp/aaaaaaaa");
    $m = Doctrine::getTable("Member")->find($cm->getMemberId());
    $addr = $m->getConfig('pc_address');
    error_log("DATA:addr=".$addr."\n", 3, "/tmp/aaaaaaaa");
    $c = $req->getParameter('community_topic_comment');

    $from = sfConfig::get('op_prefix') . "+c" . $ct->getCommunityId() . "@" . sfConfig::get('op_mail_domain');

    echo "Subject: " . 'testSubject';
    echo "From: " . $from;
    $body = $c['body'] . $member->getName() . "karano toukou.";
    echo "To: " . $addr;
    $mail = new opMailSend();
    $mail->subject = 'subject';
    $mail->body = $body;
    //$mail->send("senpai.so903@docomo.ne.jp",$from);
    //$mail->send("mamoru.tejima.0422@softbank.ne.jp",$from);
    //$mail->send("mamoru.tejima.0422@i.softbank.jp",$from);
    $mail->send($addr,$from,$from,$member->getName());

//    $mail->setBodyText('This is the text of the mail.');
//    $mail->setFrom('tejima@tejimaya.com', 'Some Sender');
//    $mail->addTo('tejima@gmail.com', 'Some Recipient');
//    $mail->setSubject('TestSubject');
//    $mail->send();
  }
}


$this->dispatcher->connect('op_action.post_execute_communityTopicComment_create','tejima');
