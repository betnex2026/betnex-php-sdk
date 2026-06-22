<?php

namespace Betnex\Exceptions;

use Exception;

class BetnexException extends Exception
{
    protected ?int $status;
    protected mixed $data;

    public function __construct(
        string $message = "Betnex Error",
        ?int $status = null,
        mixed $data = null
    ) {
        parent::__construct($message);

        $this->status = $status;
        $this->data = $data;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getData(): mixed
    {
        return $this->data;
    }

    public function toArray(): array
    {
        return [
            'name' => 'BetnexError',
            'message' => $this->getMessage(),
            'status' => $this->status,
            'data' => $this->data
        ];
    }

    public function __toString(): string
    {
        return "BetnexError: {$this->getMessage()}";
    }
}