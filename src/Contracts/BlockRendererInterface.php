<?php

namespace SpireMail\Contracts;

interface BlockRendererInterface
{
    /**
     * @param  array<string, mixed>  $props
     * @param  array<string, mixed>  $data
     */
    public function render(array $props, array $data = []): string;

    public function getType(): string;

    /**
     * @return array<string, mixed>
     */
    public function getDefaultProps(): array;
}
