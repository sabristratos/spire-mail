/**
 * Content block types that represent actual email content.
 */
export type ContentBlockType = 'text' | 'heading' | 'image' | 'button' | 'divider' | 'spacer' | 'html' | 'video' | 'social'

/**
 * All available block types in the email editor.
 */
export type BlockType = ContentBlockType | 'row'

/**
 * Available column layout presets.
 */
export type ColumnPreset = '1-column' | '2-equal' | '2-left-heavy' | '2-right-heavy' | '3-equal' | 'custom'

/**
 * Represents padding/margin values for block spacing.
 */
export interface SpacingValue {
    top?: number
    right?: number
    bottom?: number
    left?: number
}

/**
 * Base structure for all email blocks.
 */
export interface BaseBlock<T extends BlockType = BlockType, P = Record<string, unknown>> {
    id: string
    type: T
    props: P
}

/**
 * Properties for text blocks.
 */
export interface TextBlockProps {
    content: string
    variant: 'paragraph' | 'caption'
    align: 'left' | 'center' | 'right'
    color?: string
    fontSize?: number
    fontWeight?: 'normal' | 'medium' | 'semibold' | 'bold'
    lineHeight?: number
    padding?: SpacingValue
}

/**
 * A text block for displaying rich text content.
 */
export interface TextBlock extends BaseBlock<'text', TextBlockProps> {
    type: 'text'
}

/**
 * Properties for heading blocks.
 */
export interface HeadingBlockProps {
    content: string
    level: 'h1' | 'h2' | 'h3'
    align: 'left' | 'center' | 'right'
    color?: string
    fontSize?: number
    fontWeight?: 'normal' | 'medium' | 'semibold' | 'bold'
    lineHeight?: number
    padding?: SpacingValue
}

/**
 * A heading block for displaying titles and headings.
 */
export interface HeadingBlock extends BaseBlock<'heading', HeadingBlockProps> {
    type: 'heading'
}

/**
 * Properties for image blocks.
 */
export interface ImageBlockProps {
    src: string
    alt: string
    href?: string
    width?: string
    align: 'left' | 'center' | 'right'
    fluidOnMobile?: boolean
    borderRadius?: number
    padding?: SpacingValue
}

/**
 * An image block for displaying images.
 */
export interface ImageBlock extends BaseBlock<'image', ImageBlockProps> {
    type: 'image'
}

/**
 * Properties for button blocks.
 */
export interface ButtonBlockProps {
    text: string
    href: string
    variant: 'primary' | 'secondary' | 'success' | 'danger' | 'outline'
    size: 'sm' | 'md' | 'lg'
    align: 'left' | 'center' | 'right'
    backgroundColor?: string
    textColor?: string
    borderRadius?: number
    fullWidth?: boolean
    padding?: SpacingValue
}

/**
 * A button block for call-to-action elements.
 */
export interface ButtonBlock extends BaseBlock<'button', ButtonBlockProps> {
    type: 'button'
}

/**
 * Properties for divider/spacer blocks.
 */
export interface DividerBlockProps {
    style: 'solid' | 'dashed' | 'dotted' | 'none'
    color?: string
    width?: string
    align?: 'left' | 'center' | 'right'
    thickness?: number
    padding?: SpacingValue
}

/**
 * A divider block for horizontal lines or vertical spacing.
 */
export interface DividerBlock extends BaseBlock<'divider', DividerBlockProps> {
    type: 'divider'
}

/**
 * Properties for spacer blocks.
 */
export interface SpacerBlockProps {
    height: number
    backgroundColor?: string
}

/**
 * A spacer block for vertical spacing.
 */
export interface SpacerBlock extends BaseBlock<'spacer', SpacerBlockProps> {
    type: 'spacer'
}

/**
 * Properties for HTML/code blocks.
 */
export interface HtmlBlockProps {
    content: string
    padding?: SpacingValue
}

/**
 * An HTML block for raw HTML content.
 */
export interface HtmlBlock extends BaseBlock<'html', HtmlBlockProps> {
    type: 'html'
}

/**
 * Properties for video blocks.
 */
export interface VideoBlockProps {
    videoUrl: string
    thumbnailUrl?: string
    alt: string
    width?: string
    align: 'left' | 'center' | 'right'
    borderRadius?: number
    padding?: SpacingValue
}

/**
 * A video block for video embeds with thumbnail fallback.
 */
export interface VideoBlock extends BaseBlock<'video', VideoBlockProps> {
    type: 'video'
}

/**
 * Social media platform types.
 */
export type SocialPlatform = 'facebook' | 'twitter' | 'instagram' | 'linkedin' | 'youtube' | 'tiktok' | 'pinterest' | 'email'

/**
 * A single social media link.
 */
export interface SocialLink {
    platform: SocialPlatform
    url: string
    enabled: boolean
}

/**
 * Properties for social icons blocks.
 */
export interface SocialIconsBlockProps {
    links: SocialLink[]
    iconSize: 'sm' | 'md' | 'lg'
    iconStyle: 'filled' | 'outline' | 'circle'
    iconColor?: string
    align: 'left' | 'center' | 'right'
    gap: number
    padding?: SpacingValue
}

/**
 * A social icons block for social media links.
 */
export interface SocialIconsBlock extends BaseBlock<'social', SocialIconsBlockProps> {
    type: 'social'
}

/**
 * Union type of all content blocks.
 */
export type ContentBlock = TextBlock | HeadingBlock | ImageBlock | ButtonBlock | DividerBlock | SpacerBlock | HtmlBlock | VideoBlock | SocialIconsBlock

/**
 * Styling options for a column (maps to mj-column attributes).
 */
export interface ColumnStyle {
    backgroundColor?: string
    backgroundImage?: string
    backgroundSize?: 'cover' | 'contain' | 'auto'
    backgroundPosition?: string
    overlayColor?: string
    overlayOpacity?: number
    verticalAlign?: 'top' | 'middle' | 'bottom'
    padding?: SpacingValue
    borderRadius?: number
}

/**
 * Data structure for a single column within a row block.
 */
export interface ColumnData {
    id: string
    width: string
    blocks: ContentBlock[]
    style?: ColumnStyle
}

/**
 * Styling options for a row (maps to mj-section attributes).
 * Absorbs former container styling capabilities.
 */
export interface RowStyle {
    backgroundColor?: string
    backgroundImage?: string
    backgroundSize?: 'cover' | 'contain' | 'auto'
    backgroundPosition?: string
    padding?: SpacingValue
    borderRadius?: number
    textAlign?: 'left' | 'center' | 'right'
}

/**
 * Properties for row blocks (maps to mj-section).
 */
export interface RowBlockProps {
    preset: ColumnPreset
    columns: ColumnData[]
    gap?: number
    stackOnMobile?: boolean
    style?: RowStyle
}

/**
 * A row block for structuring email layouts (maps to mj-section).
 */
export interface RowBlock extends BaseBlock<'row', RowBlockProps> {
    type: 'row'
}

/**
 * Union type of all available email blocks.
 */
export type EmailBlock = ContentBlock | RowBlock

/**
 * Serializable email document structure.
 */
export interface EmailDocument {
    version: string
    rows: RowBlock[]
}

/**
 * Global email template settings.
 */
export interface EmailSettings {
    fontFamily: string
    backgroundColor: string
    contentBackgroundColor: string
    contentWidth: number
}

/**
 * Path to locate a block within the document hierarchy.
 */
export interface BlockPath {
    rowIndex: number
    columnIndex: number
    blockIndex: number
}

/**
 * Definition of an available block type in the sidebar.
 */
export interface BlockDefinition {
    type: BlockType
    label: string
    icon: unknown
    defaultProps: Record<string, unknown>
}
