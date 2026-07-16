<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\Returns\ReturnsPaginationStorefrontObject;
use Generated\Api\Storefront\Returns\ReturnsReturnTotalsStorefrontObject;
use Generated\Api\Storefront\ReturnsStorefrontResource;
use Generated\Shared\Transfer\ReturnTransfer;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Exception\SalesReturnsExceptionFactory;
use Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Reader\ReturnReaderInterface;

class ReturnsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const string URI_VAR_RETURN_REFERENCE = 'returnReference';

    protected const int DEFAULT_RETURNS_PER_PAGE = 10;

    public function __construct(
        protected ReturnReaderInterface $returnReader,
        protected SalesReturnsExceptionFactory $exceptionFactory,
    ) {
    }

    /**
     * @throws \Spryker\ApiPlatform\Exception\GlueApiException
     *
     * @return \Generated\Api\Storefront\ReturnsStorefrontResource|null
     */
    protected function provideItem(): ?object
    {
        $returnReference = (string)$this->findUriVariable(static::URI_VAR_RETURN_REFERENCE);

        if ($returnReference === '') {
            throw $this->exceptionFactory->createReturnNotFoundException();
        }

        $returnTransfer = $this->returnReader->findReturnByReferenceForCustomer(
            $returnReference,
            $this->getCustomerReference(),
        );

        if ($returnTransfer === null) {
            throw $this->exceptionFactory->createReturnNotFoundException();
        }

        return $this->buildResource($returnTransfer);
    }

    /**
     * @return array<\Generated\Api\Storefront\ReturnsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $limit = $this->getPaginationLimit(static::DEFAULT_RETURNS_PER_PAGE);
        $offset = $this->getPaginationOffset();

        $collectionTransfer = $this->returnReader->getReturnsByCustomerReference(
            $this->getCustomerReference(),
            $offset,
            $limit,
        );

        $resources = [];

        foreach ($collectionTransfer->getReturns() as $returnTransfer) {
            $resources[] = $this->buildResource($returnTransfer);
        }

        if ($resources !== []) {
            $totalCount = $collectionTransfer->getPagination()?->getNbResults() ?? count($resources);
            // Consumed by Spryker\ApiPlatform\EventSubscriber\PaginationLinksResponseSubscriber
            // to emit JSON:API top-level pagination links (first/last/prev/next).
            $resources[0]->pagination = ReturnsPaginationStorefrontObject::fromArray($this->calculatePagination($offset, $limit, $totalCount));
        }

        return $resources;
    }

    protected function buildResource(ReturnTransfer $returnTransfer): ReturnsStorefrontResource
    {
        $data = $returnTransfer->toArray(false, true);

        $resource = new ReturnsStorefrontResource();
        $resource->returnReference = $data['returnReference'] ?? null;
        $resource->customerReference = $this->getCustomerReference();
        $resource->store = $data['store'] ?? null;
        $resource->merchantReference = $data['merchantReference'] ?? null;
        $returnTotalsTransfer = $returnTransfer->getReturnTotals();
        $resource->returnTotals = $returnTotalsTransfer !== null ? ReturnsReturnTotalsStorefrontObject::fromArray($returnTotalsTransfer->toArray(true, true)) : null;
        $resource->returnItems = $this->extractReturnItems($returnTransfer);

        return $resource;
    }

    /**
     * Flattened return items kept on the parent resource for the
     * {@see \Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Relationship\ReturnItemsByReturnRelationshipResolver}.
     *
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
                'orderReference' => $returnItemTransfer->getOrderItem()?->getOrderReference(),
            ];
        }

        return $items;
    }
}
