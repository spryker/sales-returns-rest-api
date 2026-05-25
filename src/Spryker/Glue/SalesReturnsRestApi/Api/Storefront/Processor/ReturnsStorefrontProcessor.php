<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Processor;

use ArrayObject;
use Generated\Api\Storefront\ReturnsStorefrontResource;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ReturnCreateRequestTransfer;
use Generated\Shared\Transfer\ReturnItemTransfer;
use Generated\Shared\Transfer\ReturnTransfer;
use Spryker\ApiPlatform\State\Processor\AbstractStorefrontProcessor;
use Spryker\Client\SalesReturn\SalesReturnClientInterface;
use Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Exception\SalesReturnsExceptionFactory;

class ReturnsStorefrontProcessor extends AbstractStorefrontProcessor
{
    public function __construct(
        protected SalesReturnClientInterface $salesReturnClient,
        protected SalesReturnsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @param \Generated\Api\Storefront\ReturnsStorefrontResource $data
     *
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     */
    protected function processPost(mixed $data): ReturnsStorefrontResource
    {
        $returnResponseTransfer = $this->salesReturnClient->createReturn(
            $this->buildCreateRequest($data),
        );

        if (!$returnResponseTransfer->getIsSuccessful()) {
            throw $this->exceptionFactory->createExceptionFromReturnResponse($returnResponseTransfer);
        }

        return $this->mapReturnToResource($returnResponseTransfer->getReturnOrFail(), $data);
    }

    protected function buildCreateRequest(ReturnsStorefrontResource $data): ReturnCreateRequestTransfer
    {
        $returnItemTransfers = [];

        foreach ($data->returnItems ?? [] as $item) {
            $salesOrderItemUuid = is_array($item) ? ($item['salesOrderItemUuid'] ?? null) : null;
            $reason = is_array($item) ? ($item['reason'] ?? null) : null;

            $returnItemTransfers[] = (new ReturnItemTransfer())
                ->setReason($reason)
                ->setOrderItem((new ItemTransfer())->setUuid($salesOrderItemUuid));
        }

        return (new ReturnCreateRequestTransfer())
            ->setStore($data->store)
            ->setCustomer((new CustomerTransfer())->setCustomerReference($this->getCustomerReference()))
            ->setReturnItems(new ArrayObject($returnItemTransfers));
    }

    protected function mapReturnToResource(
        ReturnTransfer $returnTransfer,
        ReturnsStorefrontResource $data,
    ): ReturnsStorefrontResource {
        $returnData = $returnTransfer->toArray(false, true);

        $data->returnReference = $returnData['returnReference'] ?? null;
        $data->customerReference = $this->getCustomerReference();
        $data->store = $returnData['store'] ?? $data->store;
        $data->merchantReference = $returnData['merchantReference'] ?? null;
        $data->returnTotals = $returnTransfer->getReturnTotals()?->toArray(true, true) ?? [];
        $data->returnItems = $this->extractReturnItems($returnTransfer);

        return $data;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function extractReturnItems(ReturnTransfer $returnTransfer): array
    {
        $items = [];

        foreach ($returnTransfer->getReturnItems() as $returnItemTransfer) {
            $items[] = [
                'uuid' => $returnItemTransfer->getUuid(),
                'reason' => $returnItemTransfer->getReason(),
                'orderItemUuid' => $returnItemTransfer->getOrderItem()?->getUuid(),
            ];
        }

        return $items;
    }
}
