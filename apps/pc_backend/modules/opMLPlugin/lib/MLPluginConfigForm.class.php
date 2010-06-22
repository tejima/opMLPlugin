<?php
class MLPluginConfigForm extends sfForm
{
  protected $configs = array(
    'ml_address' => 'ml_address',
    'ml_from' => 'ml_from',
  );
  public function configure()
  {
    $this->setWidgets(array(
    'ml_address' => new sfWidgetFormInput(),
    'ml_from' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
    'ml_address' => new sfValidatorString(array(),array()),
    'ml_from' => new sfValidatorString(array(),array()),
    ));

    $this->widgetSchema->setHelp('ml_address','MLメールアドレス');
    $this->widgetSchema->setHelp('ml_from','FROMメールアドレス');

    foreach($this->configs as $k => $v)
    {
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($v);

      if($config)
      {
        $this->getWidgetSchema()->setDefault($k,$config->getValue());
      }
    }
    $this->getWidgetSchema()->setNameFormat('ml[%s]');
  }
  public function save(){
    foreach($this->getValues() as $k => $v)
    {
      if(!isset($this->configs[$k]))
      {
        continue;
      }
      $config = Doctrine::getTable('SnsConfig')->retrieveByName($this->configs[$k]);
      if(!$config)
      {
        $config = new SnsConfig();
        $config->setName($this->configs[$k]);
      }
      $config->setValue($v);
      $config->save();
    }
  }
  public function validate($validator,$value,$arguments = array())
  {
    return $value;
  }
}
?>

