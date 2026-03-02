<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\SalesReturnsRestApi\Processor\Builder;

use Generated\Shared\Transfer\ReturnCollectionTransfer;
use Generated\Shared\Transfer\ReturnFilterTransfer;
use Generated\Shared\Transfer\ReturnResponseTransfer;
use Generated\Shared\Transfer\ReturnTransfer;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;

interface RestReturnResponseBuilderInterface
{
    public function createReturnListRestResponse(
        ReturnFilterTransfer $returnFilterTransfer,
        ReturnCollectionTransfer $returnCollectionTransfer
    ): RestResponseInterface;

    public function createReturnRestResponse(ReturnTransfer $returnTransfer): RestResponseInterface;

    public function createErrorRestResponse(string $message): RestResponseInterface;

    public function createErrorRestResponseFromReturnResponse(
        ReturnResponseTransfer $returnResponseTransfer
    ): RestResponseInterface;

    /**
     * @param \Generated\Shared\Transfer\ReturnTransfer $returnTransfer
     *
     * @return array<\Spryker\Glue\GlueApplication\Rest\JsonApi\RestResourceInterface>
     */
    public function createReturnItemRestResourcesFromReturnTransfer(ReturnTransfer $returnTransfer): array;
}
