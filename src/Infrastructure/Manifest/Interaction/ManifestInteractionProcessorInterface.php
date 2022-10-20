<?php

/**
 * Copyright © 2019-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerSdk\Sdk\Infrastructure\Manifest\Interaction;

interface ManifestInteractionProcessorInterface
{
    /**
     * @param array $valueConfigMap
     *
     * @return array
     */
    public function receiveValues(array $valueConfigMap): array;
}
