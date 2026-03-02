<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\SalesReturnsRestApi\Processor\Reader;

use Generated\Shared\Transfer\FilterTransfer;
use Generated\Shared\Transfer\ReturnFilterTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;
use Spryker\Glue\SalesReturnsRestApi\Dependency\Client\SalesReturnsRestApiToSalesReturnClientInterface;
use Spryker\Glue\SalesReturnsRestApi\Processor\Builder\RestReturnResponseBuilderInterface;
use Spryker\Shared\SalesReturnsRestApi\SalesReturnsRestApiConfig as SalesReturnsRestApiSharedConfig;

class ReturnReader implements ReturnReaderInterface
{
    /**
     * @var \Spryker\Glue\SalesReturnsRestApi\Dependency\Client\SalesReturnsRestApiToSalesReturnClientInterface
     */
    protected $salesReturnClient;

    /**
     * @var \Spryker\Glue\SalesReturnsRestApi\Processor\Builder\RestReturnResponseBuilderInterface
     */
    protected $restReturnResponseBuilder;

    public function __construct(
        SalesReturnsRestApiToSalesReturnClientInterface $salesReturnClient,
        RestReturnResponseBuilderInterface $restReturnResponseBuilder
    ) {
        $this->salesReturnClient = $salesReturnClient;
        $this->restReturnResponseBuilder = $restReturnResponseBuilder;
    }

    public function getReturns(RestRequestInterface $restRequest): RestResponseInterface
    {
        if ($restRequest->getResource()->getId()) {
            return $this->getReturnAttributes($restRequest);
        }

        return $this->getReturnsAttributes($restRequest);
    }

    protected function getReturnsAttributes(RestRequestInterface $restRequest): RestResponseInterface
    {
        $returnFilterTransfer = $this->createReturnFilter($restRequest);
        $returnCollectionTransfer = $this->salesReturnClient->getReturns($returnFilterTransfer);

        return $this->restReturnResponseBuilder->createReturnListRestResponse(
            $returnFilterTransfer,
            $returnCollectionTransfer,
        );
    }

    protected function getReturnAttributes(RestRequestInterface $restRequest): RestResponseInterface
    {
        $returnFilterTransfer = $this->createReturnFilter($restRequest)
            ->setReturnReference($restRequest->getResource()->getId());

        $returnTransfer = $this->salesReturnClient->getReturns($returnFilterTransfer)
            ->getReturns()
            ->getIterator()
            ->current();

        if (!$returnTransfer) {
            return $this->restReturnResponseBuilder->createErrorRestResponse(SalesReturnsRestApiSharedConfig::ERROR_IDENTIFIER_RETURN_NOT_FOUND);
        }

        return $this->restReturnResponseBuilder->createReturnRestResponse($returnTransfer);
    }

    protected function createReturnFilter(RestRequestInterface $restRequest): ReturnFilterTransfer
    {
        $filterTransfer = new FilterTransfer();

        if ($restRequest->getPage()) {
            $filterTransfer
                ->setOffset($restRequest->getPage()->getOffset())
                ->setLimit($restRequest->getPage()->getLimit());
        }

        return (new ReturnFilterTransfer())
            ->fromArray($restRequest->getHttpRequest()->query->all(), true)
            ->setCustomerReference($restRequest->getRestUser()->getNaturalIdentifier())
            ->setFilter($filterTransfer);
    }
}
