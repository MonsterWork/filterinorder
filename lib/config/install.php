<?php

$plugin_id = array('shop', 'filterinorder');
$app_settings_model = new waAppSettingsModel();
$app_settings_model->set($plugin_id, 'status', 1);
$app_settings_model->set($plugin_id, 'is_button', 0);
