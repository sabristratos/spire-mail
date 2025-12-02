<?php

namespace SpireMail\Support;

/**
 * Centralized color constants for email template rendering.
 */
final class ThemeColors
{
    /**
     * Button variants.
     */
    public const PRIMARY = '#3b82f6';

    public const SECONDARY = '#6b7280';

    public const SUCCESS = '#10b981';

    public const DANGER = '#ef4444';

    /**
     * Text colors.
     */
    public const TEXT_DEFAULT = '#333333';

    public const TEXT_ON_PRIMARY = '#ffffff';

    /**
     * Divider/Border colors.
     */
    public const DIVIDER = '#e5e7eb';

    /**
     * Background colors.
     */
    public const BACKGROUND = '#ffffff';

    public const BACKGROUND_TRANSPARENT = 'transparent';

    /**
     * Get the button background color for a variant.
     *
     * @param  array<string, mixed>  $props
     */
    public static function getButtonBackgroundColor(array $props): string
    {
        if (! empty($props['backgroundColor'])) {
            return $props['backgroundColor'];
        }

        return match ($props['variant'] ?? 'primary') {
            'primary' => self::PRIMARY,
            'secondary' => self::SECONDARY,
            'success' => self::SUCCESS,
            'danger' => self::DANGER,
            'outline' => self::BACKGROUND_TRANSPARENT,
            default => self::PRIMARY,
        };
    }

    /**
     * Get the button text color for a variant.
     *
     * @param  array<string, mixed>  $props
     */
    public static function getButtonTextColor(array $props): string
    {
        if (! empty($props['textColor'])) {
            return $props['textColor'];
        }

        return match ($props['variant'] ?? 'primary') {
            'outline' => self::PRIMARY,
            default => self::TEXT_ON_PRIMARY,
        };
    }
}
