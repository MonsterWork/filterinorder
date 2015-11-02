<?php

class shopFilterinorderPluginBackendInfosectionAction extends waViewAction
{
  public function execute()
  {
    $get = waRequest::get();
    if(isset($get['hash'])) {
        $this->view->assign('filter_hash', $get['hash']);
        $this->view->assign('filter_view', $get['view']);
    }
  }
}