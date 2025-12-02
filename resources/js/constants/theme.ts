/**
 * Centralized theme constants for the email editor.
 * Keep in sync with PHP ThemeColors class.
 */

export const COLORS = {
    primary: '#3b82f6',
    secondary: '#6b7280',
    success: '#10b981',
    danger: '#ef4444',
    textDefault: '#333333',
    textOnPrimary: '#ffffff',
    divider: '#e5e7eb',
    background: '#ffffff',
    backgroundTransparent: 'transparent',
    link: '#3b82f6',
    blockquoteBorder: '#d1d5db',
    codeBackground: '#f3f4f6',
    codeText: '#6b7280',
    preBackground: '#1f2937',
} as const

export const ALIGNMENT_OPTIONS = {
    horizontal: [
        { value: 'left', label: 'Left' },
        { value: 'center', label: 'Center' },
        { value: 'right', label: 'Right' },
    ],
    vertical: [
        { value: 'top', label: 'Top' },
        { value: 'middle', label: 'Middle' },
        { value: 'bottom', label: 'Bottom' },
    ],
} as const

export const TEXT_VARIANTS = [
    { value: 'paragraph', label: 'Paragraph' },
    { value: 'caption', label: 'Caption' },
] as const

export const HEADING_LEVELS = [
    { value: 'h1', label: 'H1' },
    { value: 'h2', label: 'H2' },
    { value: 'h3', label: 'H3' },
] as const

export const BUTTON_VARIANTS = [
    { value: 'primary', label: 'Primary' },
    { value: 'secondary', label: 'Secondary' },
    { value: 'success', label: 'Success' },
    { value: 'danger', label: 'Danger' },
    { value: 'outline', label: 'Outline' },
] as const

export const BUTTON_SIZES = [
    { value: 'sm', label: 'Small' },
    { value: 'md', label: 'Medium' },
    { value: 'lg', label: 'Large' },
] as const

export const FONT_WEIGHTS = [
    { value: 'normal', label: 'Normal' },
    { value: 'medium', label: 'Medium' },
    { value: 'semibold', label: 'Semibold' },
    { value: 'bold', label: 'Bold' },
] as const

export const DIVIDER_STYLES = [
    { value: 'solid', label: 'Solid' },
    { value: 'dashed', label: 'Dashed' },
    { value: 'dotted', label: 'Dotted' },
    { value: 'none', label: 'Spacer Only' },
] as const

export const COLUMN_PRESETS = [
    { value: '1-column', label: '1 Column (100%)' },
    { value: '2-equal', label: '2 Columns (50/50)' },
    { value: '2-left-heavy', label: '2 Columns (66/34)' },
    { value: '2-right-heavy', label: '2 Columns (34/66)' },
    { value: '3-equal', label: '3 Columns (33/33/33)' },
    { value: 'custom', label: 'Custom Widths' },
] as const

export const DIVIDER_WIDTHS = [
    { value: '100%', label: 'Full Width' },
    { value: '75%', label: '75%' },
    { value: '50%', label: '50%' },
    { value: '25%', label: '25%' },
] as const

export const BACKGROUND_SIZE_OPTIONS = [
    { value: 'cover', label: 'Cover' },
    { value: 'contain', label: 'Contain' },
    { value: 'auto', label: 'Auto' },
] as const

export const DEFAULT_PADDING = {
    top: 10,
    right: 0,
    bottom: 10,
    left: 0,
} as const

export const DEFAULT_CONTAINER_PADDING = {
    top: 20,
    right: 20,
    bottom: 20,
    left: 20,
} as const

export const DEFAULT_DIVIDER_PADDING = {
    top: 20,
    right: 0,
    bottom: 20,
    left: 0,
} as const

export const DEFAULT_COLUMNS_PADDING = {
    top: 0,
    right: 0,
    bottom: 0,
    left: 0,
} as const

export const DEFAULT_ROW_PADDING = {
    top: 10,
    right: 10,
    bottom: 10,
    left: 10,
} as const

export type AlignmentHorizontal = (typeof ALIGNMENT_OPTIONS.horizontal)[number]['value']
export type AlignmentVertical = (typeof ALIGNMENT_OPTIONS.vertical)[number]['value']
export type TextVariant = (typeof TEXT_VARIANTS)[number]['value']
export type HeadingLevel = (typeof HEADING_LEVELS)[number]['value']
export type ButtonVariant = (typeof BUTTON_VARIANTS)[number]['value']
export type ButtonSize = (typeof BUTTON_SIZES)[number]['value']
export type FontWeight = (typeof FONT_WEIGHTS)[number]['value']
export type DividerStyle = (typeof DIVIDER_STYLES)[number]['value']
export type DividerWidth = (typeof DIVIDER_WIDTHS)[number]['value']
export type BackgroundSize = (typeof BACKGROUND_SIZE_OPTIONS)[number]['value']
export type ColumnPreset = (typeof COLUMN_PRESETS)[number]['value']
