# ZATCA API PHP Client

This is a simple PHP library to work with the ZATCA API. You can send invoice data, check invoice status, verify compliance, and manage certificates easily.

---

## Features

- Supports all major ZATCA API endpoints
- Simple authentication with certificate and secret
- Response handling via typed response objects
- Environment support: sandbox, simulation, production
- Uses any PSR-18 compatible HTTP client (e.g., Guzzle)

---

## Installation
```bash
composer require sevaske/zatca-api
```

---

## Quick Start

```php
use GuzzleHttp\Client;
use Sevaske\ZatcaApi\Api;
use Sevaske\ZatcaApi\Responses\ClearanceResponse;
use Sevaske\ZatcaApi\Responses\ComplianceCertificateResponse;
use Sevaske\ZatcaApi\Responses\ProductionCertificateResponse;
use Sevaske\ZatcaApi\Responses\ReportingResponse;
use Sevaske\ZatcaApi\Exceptions\ZatcaRequestException;

$httpClient = new Client();

$api = new Api('simulation', $httpClient);

try {
    $response = $api->complianceCertificate('my csr', 'otp code'); // ComplianceCertificateResponse
    
    if (! $response->success()) {
        // handle
    }
    
    $credentials = [
        'requestId' => $response->requestId(),
        'secret' => $response->secret(),
        'certificate' => $response->certificate(),
    ];
    
    // saving the certificate
    file_put_contents('credentials.json', json_encode($credentials));
    
    // set the credentials to auth
    $api->setCredentials($credentials['certificate'], $credentials['secret']);
    
    $productionCertificate = $api->productionCertificate($credentials['requestId']); // ProductionCertificateResponse
    
    if (! $productionCertificate->success()) {
        // handle
    }
    
    $productionCredentials = [
        'requestId' => $productionCertificate->requestId(),
        'secret' => $productionCertificate->secret(),
        'certificate' => $productionCertificate->certificate(),
    ];
    
    // saving the certificate
    file_put_contents('production-credentials.json', json_encode($productionCredentials));
    
    // set the credentials to auth
    $api->setCredentials($productionCredentials['certificate'], $productionCredentials['secret']);
    
    $reportingResponse = $api->reporting('signed-xml-invoice', 'invoice-hash', 'uuid', true); // ReportingResponse
    $reportingResponse->success();
    $reportingResponse->warnings();
    
    $clearanceResponse = $api->clearance('signed-xml-invoice', 'invoice-hash', 'uuid', true); // ClearanceResponse
    $reportingResponse->success();
    $reportingResponse->warnings();
} catch (ZatcaRequestException $e) {
    echo "Request error: " . $e->getMessage();
    print_r($e->context());
}
```

---

## Available Methods

| Method                      | Description                                       |
|-----------------------------|-------------------------------------------------|
| `reporting()`               | Submit invoice data to ZATCA                      |
| `clearance()`               | Request invoice clearance status                   |
| `compliance()`              | Verify invoice compliance                          |
| `complianceCertificate()`  | Request compliance certificate                     |
| `productionCertificate()`  | Obtain production certificate                      |
| `renewProductionCertificate()` | Renew production certificate                    |

---

## Exception handling

The library throws the following exceptions which you can catch and handle:

- `ZatcaException` — general exception class
- `ZatcaRequestException` — errors during the HTTP request
- `ZatcaResponseException` — errors processing the API response

---
