<?php
/** @var modX $modx */
/** @var array $sources */
$settings = array();
$tmp = array(
    'main' => array(
        'ga_tracking_id' => array(
            'xtype' => 'textfield',
            'value' => '',
        ),
        'currency' => array(
            'xtype' => 'textfield',
            'value' => 'RUB',
        ),
        'shop_name' => array(
            'xtype' => 'textfield',
            'value' => '[[++site_name]]',
        ),
    ),
);

foreach ($tmp as $area => $rows) {
    foreach ($rows as $k => $v) {
        /** @var modSystemSetting $setting */
        $setting = $modx->newObject('modSystemSetting');
        $setting->fromArray(array_merge(array(
            'namespace' => PKG_NAME_LOWER,
            'area' => PKG_NAME_SHORT . '_' . $area,
            'key' => PKG_NAME_SHORT . '_' . $k,
        ), $v), '', true, true);

        $settings[] = $setting;
    }
}
unset($tmp, $area, $rows, $k, $v);

return $settings;