<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Vendor\CustomOrderProcessing\Api\OrderStatusLoggerInterface;
use Vendor\CustomOrderProcessing\Api\EmailNotifierInterface;

class SalesOrderSaveAfter implements ObserverInterface
{
    /** @var OrderStatusLoggerInterface */
    protected $statusLogger;

    /** @var EmailNotifierInterface */
    protected $emailNotifier;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * Constructor
     *
     * @param OrderStatusLoggerInterface $statusLogger
     * @param EmailNotifierInterface $emailNotifier
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderStatusLoggerInterface $statusLogger,
        EmailNotifierInterface $emailNotifier,
        LoggerInterface $logger
    ) {
        $this->statusLogger = $statusLogger;
        $this->emailNotifier = $emailNotifier;
        $this->logger = $logger;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();

        $originalStatus = $order->getOrigData('status');
        $newStatus = $order->getStatus();

        if ($originalStatus !== $newStatus && $originalStatus) {
            try {
                $this->statusLogger->logStatusChange($order, $originalStatus, $newStatus);
            } catch (\Exception $e) {
                $this->logger->error(__('Order status log failed: %1', $e->getMessage()));
            }

            if ($newStatus === 'complete') {
                try {
                    $this->emailNotifier->notifyShipped($order);
                } catch (\Exception $e) {
                    $this->logger->error(__('Failed to send shipped email: %1', $e->getMessage()));
                }
            }
        }
    }
}
