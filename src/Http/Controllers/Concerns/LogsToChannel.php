<?php

namespace SpireMail\Http\Controllers\Concerns;

trait LogsToChannel
{
    /**
     * Get the configured logging channel for Spire Mail operations.
     */
    protected function logChannel(): string
    {
        return config('spire-mail.logging.channel', 'spire-mail');
    }
}
