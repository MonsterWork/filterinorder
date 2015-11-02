<?php
return array(
    'name' => 'Фильтрация заказов',
    'version' => '1.0',
    'vendor' => 985331,
    'shop_settings' => true,
    'handlers' =>
        array(
            'orders_collection' => 'ordersCollection',
            'backend_orders' => 'backendOrders',
            'backend_order' => 'backendOrder',
        ),
);
