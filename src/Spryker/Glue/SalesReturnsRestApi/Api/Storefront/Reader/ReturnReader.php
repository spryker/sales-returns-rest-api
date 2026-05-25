<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\ReturnCollectionTransfer;
use Generated\Shared\Transfer\ReturnFilterTransfer;
use Generated\Shared\Transfer\ReturnTransfer;
use Spryker\Client\SalesReturn\SalesReturnClientInterface;

class ReturnReader implements ReturnReaderInterface
{
    public function __construct(
        protected SalesReturnClientInterface $salesReturnClient,
    ) {
    }

    public function getReturnsByCustomerReference(string $customerReference, int $offset, int $limit): ReturnCollectionTransfer
    {
        $returnFilterTransfer = (new ReturnFilterTransfer())
            ->setCustomerReference($customerReference)
            ->setFilter(
                (new FilterTransfer())
                    ->setOffset($offset)
                    ->setLimit($limit),
            );

        return $this->salesReturnClient->getReturns($returnFilterTransfer);
    }

    public function findReturnByReferenceForCustomer(string $returnReference, string $customerReference): ?ReturnTransfer
    {
        $returnFilterTransfer = (new ReturnFilterTransfer())
            ->setReturnReference($returnReference)
            ->setCustomerReference($customerReference);

        $returnCollectionTransfer = $this->salesReturnClient->getReturns($returnFilterTransfer);

        foreach ($returnCollectionTransfer->getReturns() as $returnTransfer) {
            return $returnTransfer;
        }

        return null;
    }
}
