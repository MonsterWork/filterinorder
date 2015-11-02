<?php

class shopFilterinorderPluginSettingsAction extends waViewAction
{
    
    public function execute()
    {
        $model_settings = new waAppSettingsModel();
        $settings = $model_settings->get($key = array('shop', 'filterinorder')); 
        
        $this->view->assign('settings', $settings);
    }       
}
