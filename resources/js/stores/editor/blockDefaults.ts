import type { BlockType, ColumnPreset, ColumnData, EmailBlock, RowBlock } from '../../types/blocks'
import { generateId } from './state'
import {
    COLORS,
    DEFAULT_PADDING,
    DEFAULT_DIVIDER_PADDING,
    DEFAULT_ROW_PADDING,
} from '../../constants'

/**
 * Returns the column widths for a given preset.
 */
export function getPresetWidths(preset: ColumnPreset): string[] {
    switch (preset) {
        case '1-column':
            return ['100%']
        case '2-equal':
            return ['50%', '50%']
        case '2-left-heavy':
            return ['66%', '34%']
        case '2-right-heavy':
            return ['34%', '66%']
        case '3-equal':
            return ['33.33%', '33.34%', '33.33%']
        case 'custom':
        default:
            return ['50%', '50%']
    }
}

/**
 * Returns the default properties for a given block type.
 */
export function getDefaultProps(type: BlockType): Record<string, unknown> {
    switch (type) {
        case 'text':
            return {
                content: '',
                variant: 'paragraph',
                align: 'left',
                color: COLORS.textDefault,
                fontSize: 16,
                lineHeight: 1.5,
                fontWeight: 'normal',
                padding: { ...DEFAULT_PADDING },
            }
        case 'heading':
            return {
                content: '',
                level: 'h1',
                align: 'left',
                color: COLORS.textDefault,
                fontSize: 28,
                lineHeight: 1.2,
                fontWeight: 'bold',
                padding: { ...DEFAULT_PADDING },
            }
        case 'image':
            return {
                src: '',
                alt: '',
                href: '',
                width: '100%',
                align: 'center',
                fluidOnMobile: true,
                borderRadius: 0,
                padding: { ...DEFAULT_PADDING },
            }
        case 'button':
            return {
                text: 'Click Here',
                href: '#',
                variant: 'primary',
                size: 'md',
                align: 'center',
                borderRadius: 6,
                fullWidth: false,
                padding: { ...DEFAULT_PADDING },
            }
        case 'divider':
            return {
                style: 'solid',
                color: COLORS.divider,
                width: '100%',
                align: 'center',
                thickness: 1,
                padding: { ...DEFAULT_DIVIDER_PADDING },
            }
        case 'spacer':
            return {
                height: 40,
                backgroundColor: 'transparent',
            }
        case 'html':
            return {
                content: '',
                padding: { ...DEFAULT_PADDING },
            }
        case 'video':
            return {
                videoUrl: '',
                thumbnailUrl: '',
                alt: 'Video',
                width: '100%',
                align: 'center',
                borderRadius: 0,
                padding: { ...DEFAULT_PADDING },
            }
        case 'social':
            return {
                links: [
                    { platform: 'facebook', url: '', enabled: false },
                    { platform: 'twitter', url: '', enabled: false },
                    { platform: 'instagram', url: '', enabled: false },
                    { platform: 'linkedin', url: '', enabled: false },
                    { platform: 'youtube', url: '', enabled: false },
                    { platform: 'tiktok', url: '', enabled: false },
                    { platform: 'pinterest', url: '', enabled: false },
                    { platform: 'email', url: '', enabled: false },
                ],
                iconSize: 'md',
                iconStyle: 'filled',
                align: 'center',
                gap: 12,
                padding: { ...DEFAULT_PADDING },
            }
        case 'row':
            return {
                preset: '2-equal',
                columns: createColumnsFromPreset('2-equal'),
                gap: 10,
                stackOnMobile: true,
                style: {
                    padding: { ...DEFAULT_ROW_PADDING },
                },
            }
        default:
            return {}
    }
}

/**
 * Creates column data structures based on a preset.
 * Columns start empty - content blocks are added by user.
 */
export function createColumnsFromPreset(preset: ColumnPreset): ColumnData[] {
    const widths = getPresetWidths(preset)
    return widths.map((width) => ({
        id: generateId(),
        width,
        blocks: [],
    }))
}

/**
 * Recursively searches for a block by ID in both top-level blocks and nested column blocks.
 */
export function findBlockRecursively(blocks: EmailBlock[], id: string): EmailBlock | null {
    for (const block of blocks) {
        if (block.id === id) return block
        if (block.type === 'row') {
            for (const column of (block as RowBlock).props.columns) {
                for (const nestedBlock of column.blocks) {
                    if (nestedBlock.id === id) return nestedBlock as EmailBlock
                }
            }
        }
    }
    return null
}
