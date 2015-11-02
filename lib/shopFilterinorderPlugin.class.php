<?php

class shopFilterinorderPlugin extends shopPlugin
{
    public function backendOrders()
    {
        if (!$this->getSettings('status')) {
            return false;
        }
        
        $view = wa()->getView();
        $plugin_model = new shopPluginModel();
        $workflow = new shopWorkflow();

        $model_settings = new waAppSettingsModel();
        $settings = $model_settings->get($key = array('shop', 'filterinorder'));
        $currency_info = waCurrency::getInfo(wa()->getConfig()->getCurrency());
        $view->assign('settings', $settings);
        $view->assign('currency_html', $currency_info['sign_html']);
        $view->assign('prices', $this->getPriceRanges());
        $view->assign('states', $workflow->getAvailableStates());
        $view->assign('payments', $plugin_model->listPlugins(shopPluginModel::TYPE_PAYMENT));
        $view->assign('shippings', $plugin_model->listPlugins(shopPluginModel::TYPE_SHIPPING));
        return array('sidebar_section' => $view->fetch($this->path . '/templates/actions/backend/BackendOrders.html'));
    }

    public function backendOrder()
    {
        if (!$this->getSettings('status')) {
            return false;
        }
        if (waRequest::get('hash')) {
            return array('info_section' => wa()->getView()->fetch($this->path . '/templates/actions/backend/BackendOrder.html'));
        }
        return null;
    }

    public function ordersCollection($params)
    {
        /**
         * @var shopOrdersCollection $collection
         */

        $collection = $params['collection'];
        $hash = $collection->getType();
        $filters = self::parseHash(urldecode($hash));
        
        $model = new shopOrderModel();
        
        $default_currency = wa()->getConfig()->getCurrency();        
        $operators = array(
            "_from" => ">=",
            "_to" => "<="
        );
        
        foreach ($filters as $k => $v) {
            $key = $model->escape($k);
            $value = $model->escape($v);
            if (empty($value))
                continue;
            if (substr($key, 0, 15) == 'update_datetime') {
                if (array_key_exists(substr($k, 15), $operators)) {
                    $collection->addWhere("o.update_datetime".$operators[substr($k, 15)]."'".date('Y-m-d', strtotime($v."+1day"))."'");
                }
            } elseif (substr($key, 0, 5) == 'price') {
                if (array_key_exists(substr($k, 5), $operators)) {
                    $collection->addWhere("(CASE WHEN o.currency = '".$default_currency."' THEN o.total ELSE o.total*o.rate END)".$operators[substr($k, 5)]."'".$v."'");  
                }
            } elseif (substr($key, 0, 7) == 'params.') {
                $model_params = new shopOrderParamsModel();
                $params_table_name = $model_params->getTableName();
                $collection->addJoin(
                    $params_table_name,
                    "o.id=:table.order_id AND :table.name='".substr($key, 7)."'",
                    ":table.value".$this->getWhere($value));
            } elseif ($model->fieldExists($key)) {
                $collection->addWhere("o.".$key.$this->getWhere($value));
            };
        }
        return true;
    }

    protected function parseHash($str)
    {
        $parse = explode('&', $str);
        $array = array();
        foreach ($parse as $key => $p) {
            $result = explode('=', $p);
            $min_parse = explode('[', $result[0]);
            if (isset($min_parse[1])) {
                $end_parse = explode(']', $min_parse[1]);
                $array[$min_parse[0]][] = $end_parse[0];
            } else {
                $array[$result[0]] = $result[1];
            }
        }
        return $array;
    }

    protected static function getWhere($var)
    {
        $where = "";
        if (is_array($var)) {
            if (count($var) > 1) {
                $where = " IN ('".implode("','", $var)."')";
            } else {
                $where = " = '".$var[0]."'";
            }
        }
        return $where;
    }
    
    protected static function getPriceRanges() {
        $default_currency = wa()->getConfig()->getCurrency();
        $prices = array();
        
        $order_model = new shopOrderModel();
        $prices = $order_model->query("SELECT MIN(total) min, MAX(total) max FROM shop_order WHERE currency = '".$default_currency."'")->fetchAssoc();
        $prices['max'] = ceil($prices['max']);
        $prices['min'] = floor($prices['min']);
        
        $orders_not_default_currency = $order_model->query("SELECT currency, total, rate FROM shop_order WHERE currency <> '".$default_currency."'")->fetchAll();
        foreach($orders_not_default_currency as $key => $ndc) {
            if($ndc['total'] * $ndc['rate'] > $prices['max']) {
                $prices['max'] = ceil($ndc['total'] * $ndc['rate']);
            } else if ($ndc['total'] * $ndc['rate'] < $prices['min']) {
                $prices['min'] = floor($ndc['total'] * $ndc['rate']);
            }
        }
        return $prices;
    }
}
