<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Exception;

use Generated\Shared\Transfer\ReturnResponseTransfer;
use Spryker\ApiPlatform\Exception\GlueApiException;
use Spryker\Glue\SalesReturnsRestApi\SalesReturnsRestApiConfig;
use Spryker\Shared\SalesReturnsRestApi\SalesReturnsRestApiConfig as SalesReturnsRestApiSharedConfig;
use Symfony\Component\HttpFoundation\Response;

class SalesReturnsExceptionFactory
{
    public function __construct(
        protected SalesReturnsRestApiConfig $salesReturnsRestApiConfig,
    ) {
    }

    public function createReturnNotFoundException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_NOT_FOUND,
            SalesReturnsRestApiConfig::RESPONSE_CODE_CANT_FIND_RETURN,
            SalesReturnsRestApiConfig::RESPONSE_MESSAGE_CANT_FIND_RETURN,
        );
    }

    public function createReturnCantBeCreatedException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            SalesReturnsRestApiConfig::RESPONSE_CODE_RETURN_CANT_BE_CREATED,
            SalesReturnsRestApiConfig::RESPONSE_MESSAGE_RETURN_CANT_BE_CREATED,
        );
    }

    public function createReturnFromMultipleMerchantsException(): GlueApiException
    {
        return new GlueApiException(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            SalesReturnsRestApiConfig::RESPONSE_CODE_RETURN_CANT_BE_FROM_MULTIPLE_MERCHANTS,
            SalesReturnsRestApiConfig::RESPONSE_MESSAGE_CANT_RETURN_FOR_MULTIPLE_MERCHANTS,
        );
    }

    /**
     * Maps `ReturnResponseTransfer` messages to a `GlueApiException`. Identifiers come from the
     * shared config mapping; unknown messages fall back to the "can't be created" 422 default.
     */
    public function createExceptionFromReturnResponse(ReturnResponseTransfer $returnResponseTransfer): GlueApiException
    {
        $errorMessageToIdentifier = $this->salesReturnsRestApiConfig->getErrorMessageToErrorIdentifierMapping();

        foreach ($returnResponseTransfer->getMessages() as $messageTransfer) {
            $message = (string)$messageTransfer->getValue();
            $identifier = $errorMessageToIdentifier[$message] ?? null;

            if ($identifier === SalesReturnsRestApiSharedConfig::ERROR_IDENTIFIER_MERCHANT_RETURN_ITEMS_FROM_DIFFERENT_MERCHANTS) {
                return $this->createReturnFromMultipleMerchantsException();
            }

            if ($identifier === SalesReturnsRestApiSharedConfig::ERROR_IDENTIFIER_RETURN_NOT_FOUND) {
                return $this->createReturnNotFoundException();
            }
        }

        return $this->createReturnCantBeCreatedException();
    }
}
