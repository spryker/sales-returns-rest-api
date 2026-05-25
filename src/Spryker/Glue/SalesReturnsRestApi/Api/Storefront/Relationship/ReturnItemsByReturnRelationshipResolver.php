<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

declare(strict_types=1);

namespace Spryker\Glue\SalesReturnsRestApi\Api\Storefront\Relationship;

use Generated\Api\Storefront\ReturnItemsStorefrontResource;
use Spryker\ApiPlatform\Relationship\AbstractRelationshipResolver;

class ReturnItemsByReturnRelationshipResolver extends AbstractRelationshipResolver
{
    /**
     * @return array<int, \Generated\Api\Storefront\ReturnItemsStorefrontResource>
     */
    protected function resolveRelationship(): array
    {
        $resources = [];

        foreach ($this->getParentResources() as $parentResource) {
            $returnItems = $parentResource->returnItems ?? null;

            if (!is_array($returnItems)) {
                continue;
            }

            foreach ($returnItems as $returnItem) {
                if (!is_array($returnItem)) {
                    continue;
                }

                $resource = new ReturnItemsStorefrontResource();
                $resource->uuid = $returnItem['uuid'] ?? null;
                $resource->reason = $returnItem['reason'] ?? null;
                $resource->orderItemUuid = $returnItem['orderItemUuid'] ?? null;

                $resources[] = $resource;
            }
        }

        return $resources;
    }
}
