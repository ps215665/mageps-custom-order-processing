<?php

namespace Vendor\CustomOrderProcessing\Api;

interface OrderStatusUpdateInterface
{
    /**
     * Update order status
     *
     * @param string $incrementId
     * @param string $newStatus
     * @return string
     */
    public function updateStatus($incrementId, $newStatus);
}
