<?php

namespace Sevaske\ZatcaApi\Exceptions;

use Sevaske\ZatcaApi\Interfaces\ZatcaExceptionInterface;

class ZatcaException extends \Exception implements ZatcaExceptionInterface
{
    /**
     * @var array
     */
    protected $context = [];

    public function __construct(string $message = '', array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct($message, $code, $previous);
    }

    public function withContext(array $context): self
    {
        $this->context = array_merge($this->context, $context);

        return $this;
    }

    public function context(): array
    {
        return $this->context;
    }
}
