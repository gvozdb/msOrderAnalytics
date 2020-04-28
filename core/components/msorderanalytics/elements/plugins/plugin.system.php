<?php
/** @var modX $modx */
/** @var msOrderAnalytics $msoa */

$path = MODX_CORE_PATH . 'components/msorderanalytics/model/msorderanalytics/';
if (!is_object($modx->msorderanalytics)) {
    $msoa = $modx->getService('msorderanalytics', 'msorderanalytics', $path);
} else {
    $msoa = $modx->msorderanalytics;
}
$className = 'msoa' . $modx->event->name;
$modx->loadClass('msoaPlugin', $msoa->config['pluginsPath'], true, true);
$modx->loadClass($className, $msoa->config['pluginsPath'], true, true);
if (class_exists($className)) {
    $handler = new $className($modx, $scriptProperties);
    $handler->run();
} else {
    // Удаляем событие у плагина, если такого класса не существует
    $event = $modx->getObject('modPluginEvent', array(
        'pluginid' => $modx->event->plugin->get('id'),
        'event' => $modx->event->name,
    ));
    if ($event instanceof modPluginEvent) {
        $event->remove();
    }
}
return;