<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\SalesReturnsRestApi\Processor\Mapper;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\RestErrorMessageTransfer;
use Generated\Shared\Transfer\RestReturnItemsAttributesTransfer;
use Generated\Shared\Transfer\RestReturnsAttributesTransfer;
use Generated\Shared\Transfer\ReturnItemTransfer;
use Generated\Shared\Transfer\ReturnTransfer;

interface ReturnResourceMapperInterface
{
    public function mapMessageTransferToRestErrorMessageTransfer(
        MessageTransfer $messageTransfer,
        RestErrorMessageTransfer $restErrorMessageTransfer
    ): RestErrorMessageTransfer;

    public function mapReturnTransferToRestReturnsAttributesTransfer(
        ReturnTransfer $returnTransfer,
        RestReturnsAttributesTransfer $restReturnsAttributesTransfer
    ): RestReturnsAttributesTransfer;

    public function mapReturnItemTransferToRestReturnItemsAttributesTransfer(
        ReturnItemTransfer $returnItemTransfer,
        RestReturnItemsAttributesTransfer $restReturnItemsAttributesTransfer
    ): RestReturnItemsAttributesTransfer;
}
