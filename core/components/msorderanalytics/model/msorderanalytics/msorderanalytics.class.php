<?php

class msOrderAnalytics
{
    public $config = array();
    public $initialized = array();
    /** @var modX $modx */
    public $modx;
    /** @var msoaGA $ga */
    public $ga;
    /** @var msoaTools $tools */
    public $tools;
    /** @var pdoTools $pdoTools */
    public $pdoTools;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx = &$modx;

        $corePath = $this->modx->getOption('msoa_core_path', $config, MODX_CORE_PATH . 'components/msorderanalytics/');

        $this->config = array_merge(array(
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'pluginsPath' => $corePath . 'plugins/',
            'handlersPath' => $corePath . 'handlers/',
        ), $config);

        $this->modx->addPackage('msorderanalytics', $this->config['modelPath']);
        $this->modx->lexicon->load('msorderanalytics:default');
    }

    /**
     * @param string $ctx
     * @param array  $sp
     *
     * @return boolean
     */
    public function initialize($ctx = 'web', $sp = array())
    {
        $this->config = array_merge($this->config, $sp, array('ctx' => $ctx));
        if ($pdoTools = $this->getPdoTools()) {
            $pdoTools->setConfig($this->config);
        }
        $this->getTools();

        return ($this->initialized[$ctx] = true);
    }

    /**
     * @return msoaGA
     */
    public function getGA()
    {
        if (!is_object($this->ga)) {
            if ($class = $this->modx->loadClass('ga.msoaGA', $this->config['handlersPath'], true, true)) {
                $this->ga = new $class($this->modx, $this->config);
            }
        }

        return $this->ga;
    }

    /**
     * @return msoaTools
     */
    public function getTools()
    {
        if (!is_object($this->tools)) {
            if ($class = $this->modx->loadClass('tools.msoaTools', $this->config['handlersPath'], true, true)) {
                $this->tools = new $class($this->modx, $this->config);
            }
        }

        return $this->tools;
    }

    /**
     * @return pdoTools
     */
    public function getPdoTools()
    {
        if (class_exists('pdoTools') && !is_object($this->pdoTools)) {
            $this->pdoTools = $this->modx->getService('pdoTools');
        }

        return $this->pdoTools;
    }
}