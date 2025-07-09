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
use Sevaske\ZatcaApi\Responses\ProductionCertificate;
use Sevaske\ZatcaApi\Responses\RenewalProductionCertificate;
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
     * @param  ZatcaEnvironmentEnum|string  $environment  The environment to make requests (production|emulation|sandbox).
     * @param  ClientInterface  $httpClient  The HTTP client for sending requests.
     * @param  ?string  $certificate  The certificate for auth.
     * @param  ?string  $secret  The secret of the certificate for auth.
     */
    public function __construct(
        ZatcaEnvironmentEnum|string $environment,
        ClientInterface $httpClient,
        ?string $certificate = null,
        ?string $secret = null,
    ) {
        if (is_string($environment)) {
            $environment = ZatcaEnvironmentEnum::from($environment);
        }

        $this->environment = $environment;
        $this->baseUrl = $environment->url();
        $this->httpClient = $httpClient;

        $this->setCredentials($certificate, $secret);
    }

    /**
     * @throws ZatcaRequestException|ZatcaException
     */
    public function reporting(string $signedInvoice, string $invoiceHash, string $uuid, bool $clearanceStatus = true): ReportingResponse
    {
        $response = $this->request(
            endpoint: ZatcaEndpointEnum::Reporting->value,
            payload: [
                'invoiceHash' => $invoiceHash,
                'uuid' => $uuid,
                'invoice' => base64_encode($signedInvoice),
            ],
            headers: [
                'Clearance-Status' => $clearanceStatus ? 1 : 0,
            ],
            authToken: true,
            method: 'POST',
        );

        return new ReportingResponse($response);
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaRequestException
     */
    public function clearance(string $signedInvoice, string $invoiceHash, string $uuid, bool $clearanceStatus = true): ClearanceResponse
    {
        $response = $this->request(
            endpoint: ZatcaEndpointEnum::Clearance->value,
            payload: [
                'invoiceHash' => $invoiceHash,
                'uuid' => $uuid,
                'invoice' => base64_encode($signedInvoice),
            ],
            headers: [
                'Clearance-Status' => $clearanceStatus ? 1 : 0,
            ],
            authToken: true,
            method: 'POST',
        );

        return new ClearanceResponse($response);
    }

    /**
     * Compliance Invoice
     *
     * @throws ZatcaException
     * @throws ZatcaRequestException
     */
    public function compliance(string $signedInvoice, string $invoiceHash, string $uuid): ComplianceResponse
    {
        $response = $this->request(
            endpoint: ZatcaEndpointEnum::Compliance->value,
            payload: [
                'invoiceHash' => $invoiceHash,
                'uuid' => $uuid,
                'invoice' => base64_encode($signedInvoice),
            ],
            authToken: true,
            method: 'POST',
        );

        return new ComplianceResponse($response);
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaResponseException
     * @throws ZatcaRequestException
     */
    public function complianceCertificate(string $csr, string $otp): CertificateResponse
    {
        $response = $this->request(
            endpoint: ZatcaEndpointEnum::ComplianceCertificate->value,
            payload: ['csr' => base64_encode($csr)],
            headers: ['OTP' => $otp],
            authToken: false,
        );

        return new ComplianceCertificateResponse($response);
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaResponseException
     * @throws ZatcaRequestException
     */
    public function productionCertificate(string $complianceRequestId): ProductionCertificate
    {
        $response = $this->request(
            endpoint: ZatcaEndpointEnum::ProductionCertificate->value,
            payload: ['compliance_request_id' => $complianceRequestId],
            authToken: true,
        );

        return new ProductionCertificate($response);
    }

    /**
     * @throws ZatcaException
     * @throws ZatcaResponseException
     * @throws ZatcaRequestException
     */
    public function renewProductionCertificate(string $csr, string $otp): RenewalProductionCertificate
    {
        $response = $this->request(
            endpoint: ZatcaEndpointEnum::ProductionCertificate->value,
            payload: ['csr' => base64_encode($csr)],
            headers: ['OTP' => $otp],
            authToken: false,
        );

        return new RenewalProductionCertificate($response);
    }
}
