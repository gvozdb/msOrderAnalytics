<?php

class msoaGA
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
        $this->config = array_merge(array(
            'tracking_id' => $this->modx->getOption('msoa_ga_tracking_id'),
        ), $config);

        $path = MODX_CORE_PATH . 'components/msorderanalytics/model/msorderanalytics/';
        $this->msoa = $this->modx->getService('msorderanalytics', 'msOrderAnalytics', $path);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function send(array $params = array())
    {
        $params = array_merge(array(
            'v' => 1,
            'tid' => $this->config['tracking_id'],
            'cid' => $this->getUserId(),
        ), $params);
        if (empty($params['tid'])) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[msOrderAnalytics] Empty GA tracking id. Params: ' . print_r($params, 1));

            return false;
        }
        if (empty($params['t'])) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[msOrderAnalytics] No type of treatment. Params: ' . print_r($params, 1));

            return false;
        }
        // $this->modx->log(1, '$params ' . print_r($params, 1));

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://www.google-analytics.com/collect',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
        ));
        $response = curl_exec($curl);
        if ($errno = curl_errno($curl)) {
            $response = array(
                'errorCode' => $errno,
                'errorMessage' => curl_error($curl),
            );
            $this->modx->log(modX::LOG_LEVEL_ERROR,
                '[msOrderAnalytics] Failed to send request. Params: ' . print_r($params, 1) . ', Response: ' . print_r($response, 1));

            return false;
        }
        curl_close($curl);

        return true;
    }

    /*
     * @return string
     */
    public function getUserId()
    {
        if (!empty($this->config['user_id'])) {
            return $this->config['user_id'];
        }

        $cid = '';
        if (!empty($_COOKIE['_ga'])) {
            $cid = preg_replace('/^[^\.]+\.[^\.]+\./ui', '', $_COOKIE['_ga']);
        }
        if (empty($cid)) {
            $cid = session_id() ?: $this->genUniqUserId();
        }
        $this->config['user_id'] = $cid;

        // $this->config['user_id'] = !empty($_COOKIE['msoa_cid']) ? $_COOKIE['msoa_cid'] : (session_id() ?: $this->genUniqUserId());
        // $this->config['user_id'] = (session_id() ?: $this->genUniqUserId());

        return $this->config['user_id'];
    }

    /*
     * Генерирует уникальный ID пользователя
     *
     * @return string
     */
    private function genUniqUserId()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
    }
}