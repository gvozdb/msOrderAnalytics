<?php

/**
 *
 */
class msoaMsOnCreateOrder extends msoaPlugin
{
    public $config = array();
    /** @var msoaGA $ga */
    protected $ga;
    /** @var msOrder $order */
    protected $order;

    /**
     *
     */
    public function run()
    {
        $tools = $this->msoa->getTools();
        if (!$this->ga = $this->msoa->getGA()) {
            return;
        }
        $this->order = &$this->sp['msOrder'];
        $this->config = array(
            'currency' => $this->modx->getOption('msoa_currency', null, 'RUB'),
            'shop_name' => $tools->getChunk('@INLINE ' . $this->modx->getOption('msoa_shop_name')),
        );

        if ($this->sendOrder()) {
            $this->sendProducts();
        }
    }

    /**
     * Формируем данные заказа и отсылаем в GA
     * Документация: https://developers.google.com/analytics/devguides/collection/protocol/v1/devguide#ecom
     * @return bool
     */
    protected function sendOrder()
    {
        $data = array(
            't' => 'transaction',
            'ti' => $this->order->get('id'),
            'ta' => $this->config['shop_name'],
            'tr' => $this->order->get('cost'),
            'ts' => $this->order->get('delivery_cost'),
            'tt' => 0,
            'cu' => $this->config['currency'],
        );

        return $this->ga->send($data);
    }

    /**
     * Формируем данные товаров и отсылаем в GA
     * @return bool
     */
    protected function sendProducts()
    {
        $order_id = $this->order->get('id');
        $q = $this->modx->newQuery('msOrderProduct')
            ->setClassAlias('OrderProduct')
            ->innerJoin('msProduct', 'Product', 'Product.id = OrderProduct.product_id')
            ->innerJoin('msProductData', 'ProductData', 'ProductData.id = Product.id')
            ->innerJoin('msCategory', 'Category', 'Category.id = Product.parent')
            ->select(array(
                'OrderProduct.product_id as id',
                'OrderProduct.name as name',
                'OrderProduct.price as price',
                'OrderProduct.count as count',
                'ProductData.article as article',
                'Category.pagetitle as category',
            ))
            ->where(array(
                'OrderProduct.order_id' => $order_id,
            ));
        if ($q->prepare() && $q->stmt->execute()) {
            if ($rows = $q->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                foreach ($rows as $v) {
                    $data = array(
                        't' => 'item',
                        'ti' => $order_id,
                        'in' => $v['name'],
                        'ip' => $v['price'],
                        'iq' => $v['count'],
                        'ic' => $v['article'],
                        'iv' => $v['category'],
                        'cu' => $this->config['currency'],
                    );
                    $this->ga->send($data);
                }
            }
        }

        return true;
    }
}