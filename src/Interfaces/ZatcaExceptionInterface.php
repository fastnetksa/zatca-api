<?php

namespace Sevaske\ZatcaApi\Interfaces;

interface ZatcaExceptionInterface
{
    public function withContext(array $context);

    public function context(): array;
}
