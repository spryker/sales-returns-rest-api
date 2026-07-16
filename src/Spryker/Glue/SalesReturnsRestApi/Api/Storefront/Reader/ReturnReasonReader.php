<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Reader;

use Generated\Shared\Transfer\ReturnReasonSearchCollectionTransfer;
use Generated\Shared\Transfer\ReturnReasonSearchRequestTransfer;
use Spryker\Client\SalesReturnSearch\SalesReturnSearchClientInterface;

class ReturnReasonReader implements ReturnReasonReaderInterface
{
    /**
     * @uses \Spryker\Client\SalesReturnSearch\Plugin\Elasticsearch\ResultFormatter\ReturnReasonSearchResultFormatterPlugin::NAME
     */
    protected const string KEY_RETURN_REASON_COLLECTION = 'ReturnReasonCollection';

    public function __construct(
        protected SalesReturnSearchClientInterface $salesReturnSearchClient,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $requestParameters
     */
    public function findReturnReasons(array $requestParameters): ReturnReasonSearchCollectionTransfer
    {
        $searchResults = $this->salesReturnSearchClient->searchReturnReasons(
            (new ReturnReasonSearchRequestTransfer())->setRequestParameters($requestParameters),
        );

        $collectionTransfer = $searchResults[static::KEY_RETURN_REASON_COLLECTION] ?? null;

        return $collectionTransfer instanceof ReturnReasonSearchCollectionTransfer
            ? $collectionTransfer
            : new ReturnReasonSearchCollectionTransfer();
    }
}
