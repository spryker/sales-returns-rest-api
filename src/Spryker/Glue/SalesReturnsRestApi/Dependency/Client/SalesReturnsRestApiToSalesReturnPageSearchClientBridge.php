<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\SalesReturnsRestApi\Dependency\Client;

use Generated\Shared\Transfer\ReturnReasonSearchRequestTransfer;

class SalesReturnsRestApiToSalesReturnPageSearchClientBridge implements SalesReturnsRestApiToSalesReturnPageSearchClientInterface
{
    /**
     * @var \Spryker\Client\SalesReturnPageSearch\SalesReturnPageSearchClientInterface
     */
    protected $salesReturnPageSearchClient;

    /**
     * @param \Spryker\Client\SalesReturnPageSearch\SalesReturnPageSearchClientInterface $salesReturnPageSearchClient
     */
    public function __construct($salesReturnPageSearchClient)
    {
        $this->salesReturnPageSearchClient = $salesReturnPageSearchClient;
    }

    /**
     * @param \Generated\Shared\Transfer\ReturnReasonSearchRequestTransfer $returnReasonSearchRequestTransfer
     *
     * @return array
     */
    public function searchReturnReasons(ReturnReasonSearchRequestTransfer $returnReasonSearchRequestTransfer): array
    {
        return $this->salesReturnPageSearchClient->searchReturnReasons($returnReasonSearchRequestTransfer);
    }
}
