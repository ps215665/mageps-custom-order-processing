<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Api;

use Magento\Sales\Model\Order;

interface OrderStatusLoggerInterface
{
    /**
     * Log status change
     *
     * @param Order $order
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function logStatusChange(Order $order, string $oldStatus, string $newStatus): void;
}
