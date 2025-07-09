<?php

namespace Sevaske\ZatcaApi\Enums;

enum ZatcaEnvironmentEnum: string
{
    case Sandbox = 'sandbox';

    case Simulation = 'simulation';

    case Production = 'production';

    public function url(): string
    {
        return match ($this->value) {
            'sandbox' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal/',
            'simulation' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/',
            'production' => 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core/',
        };
    }
}
