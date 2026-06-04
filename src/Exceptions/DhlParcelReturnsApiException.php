<?php

namespace SmartDato\DhlParcelReturns\Exceptions;

use Exception;
use Saloon\Http\Response;
use Throwable;

class DhlParcelReturnsApiException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        public readonly ?string $detail = null,
        public readonly ?string $type = null,
        public readonly ?string $instance = null,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public static function fromResponse(Response $response): self
    {
        try {
            $data = $response->json();
        } catch (Throwable) {
            return new self(
                message: "DHL Parcel Returns API error: {$response->status()}",
                code: $response->status(),
            );
        }

        $title = $data['title'] ?? "DHL Parcel Returns API error: {$response->status()}";
        $detail = $data['detail'] ?? null;
        $code = isset($data['status']) && is_int($data['status']) ? $data['status'] : $response->status();

        return new self(
            message: $detail !== null ? "{$title}: {$detail}" : $title,
            code: (int) $code,
            detail: $detail,
            type: $data['type'] ?? null,
            instance: $data['instance'] ?? null,
        );
    }
}
