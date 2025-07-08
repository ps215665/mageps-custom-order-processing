<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Vendor\CustomOrderProcessing\Api\OrderStatusLoggerInterface;
use Vendor\CustomOrderProcessing\Model\OrderStatusLogFactory;
use Vendor\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as OrderStatusLogResource;
use Magento\Sales\Model\Order;

class OrderStatusLogger implements OrderStatusLoggerInterface
{
    /** @var OrderStatusLogFactory */
    private OrderStatusLogFactory $logFactory;
    /** @var OrderStatusLogResource */
    private OrderStatusLogResource $logResource;

    /**
     * Constructor
     *
     * @param OrderStatusLogFactory $logFactory
     * @param OrderStatusLogResource $logResource
     */
    public function __construct(
        OrderStatusLogFactory $logFactory,
        OrderStatusLogResource $logResource
    ) {
        $this->logFactory = $logFactory;
        $this->logResource = $logResource;
    }

    /**
     * Log Status Change
     *
     * @param Order $order
     * @param string $oldStatus
     * @param string $newStatus
     * @return void
     */
    public function logStatusChange(Order $order, string $oldStatus, string $newStatus): void
    {
        $log = $this->logFactory->create();
        $log->setData([
            'order_id'    => $order->getId(),
            'old_status'  => $oldStatus,
            'new_status'  => $newStatus,
            'changed_at'  => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        $this->logResource->save($log);
    }
}
