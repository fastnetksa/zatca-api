<?php

namespace Sevaske\ZatcaApi\Enums;

class ZatcaEndpointEnum
{
    public const REPORTING = 'invoices/reporting/single';
    public const CLEARANCE = 'invoices/clearance/single';
    public const COMPLIANCE = 'compliance/invoices';
    public const COMPLIANCE_CERTIFICATE = 'compliance';
    public const PRODUCTION_CERTIFICATE = 'production/csids';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return [
            self::REPORTING,
            self::CLEARANCE,
            self::COMPLIANCE,
            self::COMPLIANCE_CERTIFICATE,
            self::PRODUCTION_CERTIFICATE,
        ];
    }
}
