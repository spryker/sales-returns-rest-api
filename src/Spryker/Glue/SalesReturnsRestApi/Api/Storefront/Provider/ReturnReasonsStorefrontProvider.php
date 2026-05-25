<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Provider;

use Generated\Api\Storefront\ReturnReasonsStorefrontResource;
use Spryker\ApiPlatform\State\Provider\AbstractStorefrontProvider;
use Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Reader\ReturnReasonReaderInterface;

class ReturnReasonsStorefrontProvider extends AbstractStorefrontProvider
{
    protected const int DEFAULT_REASONS_PER_PAGE = 10;

    public function __construct(
        protected ReturnReasonReaderInterface $returnReasonReader,
    ) {
    }

    /**
     * @return array<\Generated\Api\Storefront\ReturnReasonsStorefrontResource>
     */
    protected function provideCollection(): array
    {
        $collectionTransfer = $this->returnReasonReader->findReturnReasons([
            static::QUERY_PARAMETER_OFFSET => $this->getPaginationOffset(),
            static::QUERY_PARAMETER_LIMIT => $this->getPaginationLimit(static::DEFAULT_REASONS_PER_PAGE),
        ]);

        $resources = [];

        foreach ($collectionTransfer->getReturnReasons() as $returnReasonSearchTransfer) {
            $resource = new ReturnReasonsStorefrontResource();
            $resource->reason = $returnReasonSearchTransfer->getName();

            $resources[] = $resource;
        }

        return $resources;
    }
}
