<?php

namespace Sevaske\ZatcaApi;

use Psr\Http\Client\ClientInterface;
use Sevaske\ZatcaApi\Enums\ZatcaEndpointEnum;
use Sevaske\ZatcaApi\Enums\ZatcaEnvironmentEnum;
use Sevaske\ZatcaApi\Exceptions\ZatcaException;
use Sevaske\ZatcaApi\Exceptions\ZatcaRequestException;
use Sevaske\ZatcaApi\Exceptions\ZatcaResponseException;
use Sevaske\ZatcaApi\Responses\CertificateResponse;
use Sevaske\ZatcaApi\Responses\ClearanceResponse;
use Sevaske\ZatcaApi\Responses\ComplianceCertificateResponse;
use Sevaske\ZatcaApi\Responses\ComplianceResponse;
use Sevaske\ZatcaApi\Responses\ProductionCertificateResponse;
use Sevaske\ZatcaApi\Responses\RenewalProductionCertificateResponse;
use Sevaske\ZatcaApi\Responses\ReportingResponse;
use Sevaske\ZatcaApi\Traits\RequestBuilder;

class Api
{
    use RequestBuilder;

    protected ZatcaEnvironmentEnum $environment;

    private ?string $certificate = null;

    private ?string $secret = null;

    /**
     * Initialize the API request with an HTTP client.
     *
     * @param ZatcaEnvironmentEnum|string $environment The environment to make requests (production|simulation|sandbox).
     * @param ClientInterface             $httpClient   The HTTP client for sending requests.
     * @param string|null                 $certificate  The certificate for auth.
     * @param string|null                 $secret       The secret of the certificate for auth.
     */
    public function __construct($environment, ClientInterface $httpClient, ?string $certificate = null, ?string $secret = null)
    {
        if (is_string($environment)) {
            $environment = ZatcaEnvironmentEnum::from($environment);
        }

        $this->environment = $environment;
        $this->baseUrl     = $environment->url();
        $this->httpClient  = $httpClient;

        $this->setCredentials($certificate, $secret);
    }

    /**
     * @throws ZatcaRequestException|ZatcaException
     */
    public function reporting(string $signedInvoice, ?string $invoiceHash, string $uuid, bool $clearanceStatus = true): ReportingResponse
    {
        $rawResponse = $this->request(
            ZatcaEndpointEnum::REPORTING,
            [
                'invoice' => base64_encode($signedInvoice),
                'invoiceHash' => $this->normalizeInvoiceHash($invoiceHash),
                'uuid' => $uuid,
            ],
            [
                'Clearance-Status' => $clearanceStatus ? 1 : 0,
            ],
            true,
            'POST'
        );
        $response = new ReportingResponse($rawResponse);

        if ($response->errors()) {
            throw new ZatcaRequestException('Request failed.', [
                'errors' => $response->errors(),
            ]);
        }

        return $response;
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaRequestException
     */
    public function clearance(string $signedInvoice, ?string $invoiceHash, string $uuid, bool $clearanceStatus = true): ClearanceResponse
    {
        $rawResponse = $this->request(
            ZatcaEndpointEnum::CLEARANCE,
            [
                'invoice' => base64_encode($signedInvoice),
                'invoiceHash' => $this->normalizeInvoiceHash($invoiceHash),
                'uuid' => $uuid,
            ],
            [
                'Clearance-Status' => $clearanceStatus ? 1 : 0,
            ],
            true,
            'POST'
        );
        $response = new ClearanceResponse($rawResponse);

        if ($response->errors()) {
            throw new ZatcaRequestException('Request failed.', [
                'errors' => $response->errors(),
            ]);
        }

        return $response;
    }

    /**
     * Compliance Invoice
     *
     * @throws ZatcaException
     * @throws ZatcaRequestException
     */
    public function compliance(string $signedInvoice, ?string $invoiceHash, string $uuid): ComplianceResponse
    {
        $rawResponse = $this->request(
            ZatcaEndpointEnum::COMPLIANCE,
            [
                'invoice' => base64_encode($signedInvoice),
                'invoiceHash' => $this->normalizeInvoiceHash($invoiceHash),
                'uuid' => $uuid,
            ],
            [],
            true,
            'POST'
        );

        $response = new ComplianceResponse($rawResponse);

        if ($response->errors()) {
            throw new ZatcaRequestException('Request failed.', [
                'errors' => $response->errors(),
            ]);
        }

        return $response;
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaResponseException
     * @throws ZatcaRequestException
     */
    public function complianceCertificate(string $csr, string $otp): CertificateResponse
    {
        $rawResponse = $this->request(
            ZatcaEndpointEnum::COMPLIANCE_CERTIFICATE,
            ['csr' => base64_encode($csr)],
            ['OTP' => $otp],
            false,
            'POST'
        );
        $response = new ComplianceCertificateResponse($rawResponse);

        if ($response->errors()) {
            throw new ZatcaRequestException('Request failed.', [
                'errors' => $response->errors(),
            ]);
        }

        return $response;
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaResponseException
     * @throws ZatcaRequestException
     */
    public function productionCertificate(string $complianceRequestId): ProductionCertificateResponse
    {
        $rawResponse = $this->request(
            ZatcaEndpointEnum::PRODUCTION_CERTIFICATE,
            ['compliance_request_id' => $complianceRequestId],
            [],
            true,
            'POST'
        );

        $response = new ProductionCertificateResponse($rawResponse);

        if ($response->errors()) {
            throw new ZatcaRequestException('Request failed.', [
                'errors' => $response->errors(),
            ]);
        }

        return $response;
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaResponseException
     * @throws ZatcaRequestException
     */
    public function renewProductionCertificate(string $csr, string $otp): RenewalProductionCertificateResponse
    {
        $rawResponse = $this->request(
            ZatcaEndpointEnum::PRODUCTION_CERTIFICATE,
            ['csr' => base64_encode($csr)],
            ['OTP' => $otp],
            false,
            'POST'
        );

        $response = new RenewalProductionCertificateResponse($rawResponse);

        if ($response->errors()) {
            throw new ZatcaRequestException('Request failed.', [
                'errors' => $response->errors(),
            ]);
        }

        return $response;
    }

    private function normalizeInvoiceHash(?string $invoiceHash): string
    {
        return $invoiceHash ?: base64_encode('0');
    }
}
