<?php

namespace Sevaske\ZatcaApi\Exceptions;

use Sevaske\ZatcaApi\Interfaces\ZatcaExceptionInterface;

class ZatcaException extends \Exception implements ZatcaExceptionInterface
{
    public function __construct(string $message = '', protected array $context = [], int $code = 0, ?\Throwable $previous = null)
    {
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
