<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\ReturnCollectionTransfer;
use Generated\Shared\Transfer\ReturnTransfer;

interface ReturnReaderInterface
{
    public function getReturnsByCustomerReference(string $customerReference, int $offset, int $limit): ReturnCollectionTransfer;

    public function findReturnByReferenceForCustomer(string $returnReference, string $customerReference): ?ReturnTransfer;
}
