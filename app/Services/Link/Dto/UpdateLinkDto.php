<?php

namespace App\Services\Link\Dto;

class UpdateLinkDto
{
    public function __construct(
        public readonly ?string $longUrl,
        public readonly ?string $title,
        public readonly ?array $tags
    ) {
    }
}
