<?php

class msoaTools
{
    public $config = array();
    /** @var modX $modx */
    protected $modx;
    /** @var msOrderAnalytics $msoa */
    protected $msoa;

    /**
     * @param $modx
     * @param $config
     */
    public function __construct(modX &$modx, &$config)
    {
        $this->modx = &$modx;
        $this->config = &$config;

        $path = MODX_CORE_PATH . 'components/msorderanalytics/model/msorderanalytics/';
        $this->msoa = $this->modx->getService('msorderanalytics', 'msOrderAnalytics', $path);
    }

    /**
     * Process and return the output from a Chunk by name.
     *
     * @param string $chunk
     * @param array  $params
     *
     * @return string
     */
    public function getChunk($chunk, array $params = array())
    {
        if ($pdoTools = $this->msoa->getPdoTools()) {
            return $pdoTools->getChunk($chunk, $params);
        }

        return $this->modx->getChunk($chunk, $params);
    }
}