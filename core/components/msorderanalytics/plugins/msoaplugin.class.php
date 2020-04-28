<?php

abstract class msoaPlugin
{
    /** @var modX $modx */
    protected $modx;
    /** @var msOrderAnalytics $msoa */
    protected $msoa;
    /** @var array $sp */
    protected $sp;

    public function __construct(&$modx, &$sp)
    {
        $this->sp = &$sp;
        $this->modx = &$modx;
        $this->msoa = $this->modx->msorderanalytics;

        if (!is_object($this->msoa)) {
            $path = MODX_CORE_PATH . 'components/msorderanalytics/model/msorderanalytics/';
            $this->msoa = $this->modx->getService('msorderanalytics', 'msorderanalytics', $path, $this->sp);
        }
        if (!$this->msoa->initialized[$this->modx->context->key]) {
            $this->msoa->initialize($this->modx->context->key);
        }
    }

    abstract public function run();
}