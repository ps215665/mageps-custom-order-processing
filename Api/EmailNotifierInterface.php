<?php

namespace Vendor\CustomOrderProcessing\Api;

use Magento\Sales\Model\Order;

interface EmailNotifierInterface
{
    /**
     * Notify Order Shipped
     *
     * @param Order $order
     * @return void
     */
    public function notifyShipped(Order $order): void;
}
