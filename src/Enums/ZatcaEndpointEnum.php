<?php

namespace Sevaske\ZatcaApi\Enums;

enum ZatcaEndpointEnum: string
{
    case Reporting = 'invoices/reporting/single';

    case Clearance = 'invoices/clearance/single';

    case Compliance = 'compliance/invoices';

    case ComplianceCertificate = 'compliance';

    case ProductionCertificate = 'production/csids';
}
