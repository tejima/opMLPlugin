<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * opMLPlugin actions.
 *
 * @package    OpenPNE
 * @subpackage opMLPlugin
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 9301 2008-05-27 01:08:46Z dwhittle $
 */
class opMLPluginActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfWebRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->form = new MLPluginConfigForm();
    if ($request->isMethod(sfWebRequest::POST))
    {
      $this->form->bind($request->getParameter('ml'));
      if ($this->form->isValid())
      {
        $this->form->save();
        $this->redirect('opMLPlugin/index');
      }
    }


  }
}
