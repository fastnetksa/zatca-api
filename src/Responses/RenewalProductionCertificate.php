<?php

namespace Sevaske\ZatcaApi\Responses;

class RenewalProductionCertificate extends ProductionCertificate
{
    public function tokenType(): ?string
    {
        return $this->getOptionalAttribute('tokenType');
    }
}
