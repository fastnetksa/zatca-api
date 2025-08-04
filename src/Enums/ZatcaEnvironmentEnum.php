<?php

namespace Sevaske\ZatcaApi\Enums;

use InvalidArgumentException;

class ZatcaEnvironmentEnum
{
    public const SANDBOX = 'sandbox';
    public const SIMULATION = 'simulation';
    public const PRODUCTION = 'production';

    /**
     * @var string
     */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function sandbox(): self
    {
        return new self(self::SANDBOX);
    }

    public static function simulation(): self
    {
        return new self(self::SIMULATION);
    }

    public static function production(): self
    {
        return new self(self::PRODUCTION);
    }

    /**
     * @param string $value
     * @return self
     */
    public static function from($value): self
    {
        $value = strtolower((string) $value);
        switch ($value) {
            case self::SANDBOX:
                return self::sandbox();
            case self::SIMULATION:
                return self::simulation();
            case self::PRODUCTION:
                return self::production();
            default:
                throw new InvalidArgumentException('Invalid environment: '.$value);
        }
    }

    public function value(): string
    {
        return $this->value;
    }

    public function url(): string
    {
        switch ($this->value) {
            case self::SANDBOX:
                return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal/';
            case self::SIMULATION:
                return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation/';
            case self::PRODUCTION:
                return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core/';
            default:
                throw new InvalidArgumentException('Invalid environment: '.$this->value);
        }
    }
}
