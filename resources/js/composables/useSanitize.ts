import DOMPurify from 'dompurify'

const ALLOWED_TAGS = [
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
]

const ALLOWED_ATTR = ['href', 'target', 'rel', 'style']

const FORBID_ATTR = [
    'onerror',
    'onclick',
    'onload',
    'onmouseover',
    'onfocus',
    'onblur',
]

/**
 * Composable for sanitizing HTML content to prevent XSS attacks.
 */
export function useSanitize() {
    /**
     * Sanitizes HTML content using DOMPurify with a strict whitelist.
     */
    function sanitize(html: string): string {
        return DOMPurify.sanitize(html, {
            ALLOWED_TAGS,
            ALLOWED_ATTR,
            FORBID_ATTR,
            ALLOW_DATA_ATTR: false,
        })
    }

    /**
     * Checks if a URL is safe (not javascript:, data:, etc.).
     */
    function isUrlSafe(url: string): boolean {
        const lowercaseUrl = url.trim().toLowerCase()
        const dangerousSchemes = ['javascript:', 'data:', 'vbscript:']

        return !dangerousSchemes.some((scheme) => lowercaseUrl.startsWith(scheme))
    }

    /**
     * Sanitizes a URL, returning a safe fallback if dangerous.
     */
    function sanitizeUrl(url: string): string {
        return isUrlSafe(url) ? url : '#'
    }

    return {
        sanitize,
        isUrlSafe,
        sanitizeUrl,
    }
}
