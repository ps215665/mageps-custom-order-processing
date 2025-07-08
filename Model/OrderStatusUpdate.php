<?php

declare(strict_types=1);

namespace Vendor\CustomOrderProcessing\Model;

use Vendor\CustomOrderProcessing\Api\OrderStatusUpdateInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory as OrderStatusCollectionFactory;

class OrderStatusUpdate implements OrderStatusUpdateInterface
{
    /** @var OrderRepositoryInterface */
    private OrderRepositoryInterface $orderRepository;

    /** @var SearchCriteriaBuilder */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /** @var FilterBuilder */
    private FilterBuilder $filterBuilder;

    /** @var OrderStatusCollectionFactory */
    private OrderStatusCollectionFactory $statusCollectionFactory;

    /**
     * Constructor
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderStatusCollectionFactory $statusCollectionFactory
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderStatusCollectionFactory $statusCollectionFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->statusCollectionFactory = $statusCollectionFactory;
    }

    /**
     * Updates the status of an order with the given increment id.
     *
     * @param string $incrementId
     * @param string $newStatus
     * @return string
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function updateStatus($incrementId, $newStatus): string
    {
        $order = $this->getOrderByIncrementId($incrementId);

        if (!$order || !$order->getEntityId()) {
            throw new LocalizedException(__('Order not found.'));
        }

        $currentState = $order->getState();
        $currentStatus = $order->getStatus();

        if ($currentStatus === $newStatus) {
            return "Order already in status: $newStatus";
        }

        $allowedStatuses = $this->getStatusesForState($currentState);

        if (!in_array($newStatus, $allowedStatuses, true)) {
            throw new LocalizedException(
                new \Magento\Framework\Phrase(
                    sprintf('Status "%s" is not allowed for order state "%s".', $newStatus, $currentState)
                )
            );
        }

        $order->setStatus($newStatus);
        $this->orderRepository->save($order);

        return "Order status updated to '$newStatus'.";
    }

    /**
     * Get Order By Increment Id
     *
     * @param string $incrementId
     * @return OrderInterface|null
     * @throws LocalizedException if no order is found with the given increment ID.
     * @throws NoSuchEntityException if no orders are found.
     */
    private function getOrderByIncrementId(string $incrementId): ?OrderInterface
    {
        $filter = $this->filterBuilder
            ->setField('increment_id')
            ->setValue($incrementId)
            ->setConditionType('eq')
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilters([$filter])
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();

        return !empty($orders) ? array_values($orders)[0] : null;
    }

    /**
     * Get Statues for Current State
     *
     * @param string $state
     * @return array
     */
    // Get valid order statuses for given order state.
    private function getStatusesForState(string $state): array
    {
        $collection = $this->statusCollectionFactory->create();
        $statuses = [];

        $collection->addStateFilter($state);

        foreach ($collection as $item) {
            if ($item->getState() === $state) {
                $statuses[] = $item->getStatus();
            }
        }

        return $statuses;
    }
}
