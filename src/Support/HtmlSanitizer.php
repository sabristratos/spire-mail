<?php

namespace SpireMail\Support;

class HtmlSanitizer
{
    /**
     * @var array<int, string>
     */
    protected const ALLOWED_TAGS = [
        'p',
        'br',
        'strong',
        'b',
        'em',
        'i',
        'u',
        'a',
        'span',
        'ul',
        'ol',
        'li',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
    ];

    /**
     * @var array<string, array<int, string>>
     */
    protected const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'span' => ['style'],
    ];

    /**
     * @var array<int, string>
     */
    protected const DANGEROUS_URL_SCHEMES = [
        'javascript:',
        'data:',
        'vbscript:',
    ];

    /**
     * Sanitizes HTML content by removing unsafe tags and attributes.
     */
    public function sanitize(string $html): string
    {
        $html = $this->stripDangerousProtocols($html);
        $html = $this->stripTags($html);
        $html = $this->sanitizeAttributes($html);

        return $html;
    }

    /**
     * Strips tags not in the allowed list.
     */
    protected function stripTags(string $html): string
    {
        $allowedTagsString = '<'.implode('><', self::ALLOWED_TAGS).'>';

        return strip_tags($html, $allowedTagsString);
    }

    /**
     * Removes dangerous URL schemes from href and src attributes.
     */
    protected function stripDangerousProtocols(string $html): string
    {
        foreach (self::DANGEROUS_URL_SCHEMES as $scheme) {
            $pattern = '/(href|src)\s*=\s*["\']?\s*'.preg_quote($scheme, '/').'/i';
            $html = preg_replace($pattern, '$1="#"', $html) ?? $html;
        }

        return $html;
    }

    /**
     * Sanitizes attributes, removing event handlers and invalid attributes.
     */
    protected function sanitizeAttributes(string $html): string
    {
        $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']?/i', '', $html) ?? $html;
        $html = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/i', '', $html) ?? $html;

        return $html;
    }

    /**
     * Validates that a URL is safe (not javascript:, data:, etc.).
     */
    public function isUrlSafe(string $url): bool
    {
        $url = trim(strtolower($url));

        foreach (self::DANGEROUS_URL_SCHEMES as $scheme) {
            if (str_starts_with($url, $scheme)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Sanitizes a URL, returning a safe fallback if dangerous.
     */
    public function sanitizeUrl(string $url): string
    {
        return $this->isUrlSafe($url) ? $url : '#';
    }
}
