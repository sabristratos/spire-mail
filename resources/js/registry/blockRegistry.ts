import { markRaw, type Component } from 'vue'
import type { BlockType, BlockDefinition } from '../types/blocks'
import { getDefaultProps } from '../stores/editorStore'

import TextBlock from '../Components/Blocks/TextBlock.vue'
import HeadingBlock from '../Components/Blocks/HeadingBlock.vue'
import ImageBlock from '../Components/Blocks/ImageBlock.vue'
import ButtonBlock from '../Components/Blocks/ButtonBlock.vue'
import DividerBlock from '../Components/Blocks/DividerBlock.vue'
import SpacerBlock from '../Components/Blocks/SpacerBlock.vue'
import HtmlBlock from '../Components/Blocks/HtmlBlock.vue'
import VideoBlock from '../Components/Blocks/VideoBlock.vue'
import SocialIconsBlock from '../Components/Blocks/SocialIconsBlock.vue'
import RowBlock from '../Components/Blocks/RowBlock.vue'

import TextProperties from '../Components/Properties/TextProperties.vue'
import HeadingProperties from '../Components/Properties/HeadingProperties.vue'
import ImageProperties from '../Components/Properties/ImageProperties.vue'
import ButtonProperties from '../Components/Properties/ButtonProperties.vue'
import DividerProperties from '../Components/Properties/DividerProperties.vue'
import SpacerProperties from '../Components/Properties/SpacerProperties.vue'
import HtmlProperties from '../Components/Properties/HtmlProperties.vue'
import VideoProperties from '../Components/Properties/VideoProperties.vue'
import SocialIconsProperties from '../Components/Properties/SocialIconsProperties.vue'
import RowProperties from '../Components/Properties/RowProperties.vue'

import {
    TextIndentIcon,
    TextCreationIcon,
    Image02Icon,
    InsertBottomImageIcon,
    DashedLine01Icon,
    DistributeVerticalCenterIcon,
    SourceCodeIcon,
    Video01Icon,
    Share01Icon,
    RowInsertIcon,
} from '@hugeicons/core-free-icons'

/**
 * Configuration for a registered block type.
 */
export interface BlockRegistration {
    type: BlockType
    label: string
    icon: Component
    category: 'content' | 'layout' | 'interactive'
    canvasComponent: Component
    propertiesComponent: Component
    isWrapper: boolean
    canNest: boolean
}

const blockRegistry = new Map<BlockType, BlockRegistration>()

function registerBlock(config: BlockRegistration): void {
    blockRegistry.set(config.type, {
        ...config,
        canvasComponent: markRaw(config.canvasComponent),
        propertiesComponent: markRaw(config.propertiesComponent),
    })
}

registerBlock({
    type: 'text',
    label: 'Text',
    icon: TextIndentIcon,
    category: 'content',
    canvasComponent: TextBlock,
    propertiesComponent: TextProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'heading',
    label: 'Heading',
    icon: TextCreationIcon,
    category: 'content',
    canvasComponent: HeadingBlock,
    propertiesComponent: HeadingProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'image',
    label: 'Image',
    icon: Image02Icon,
    category: 'content',
    canvasComponent: ImageBlock,
    propertiesComponent: ImageProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'button',
    label: 'Button',
    icon: InsertBottomImageIcon,
    category: 'interactive',
    canvasComponent: ButtonBlock,
    propertiesComponent: ButtonProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'divider',
    label: 'Divider',
    icon: DashedLine01Icon,
    category: 'content',
    canvasComponent: DividerBlock,
    propertiesComponent: DividerProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'spacer',
    label: 'Spacer',
    icon: DistributeVerticalCenterIcon,
    category: 'content',
    canvasComponent: SpacerBlock,
    propertiesComponent: SpacerProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'html',
    label: 'HTML',
    icon: SourceCodeIcon,
    category: 'content',
    canvasComponent: HtmlBlock,
    propertiesComponent: HtmlProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'video',
    label: 'Video',
    icon: Video01Icon,
    category: 'content',
    canvasComponent: VideoBlock,
    propertiesComponent: VideoProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'social',
    label: 'Social Icons',
    icon: Share01Icon,
    category: 'content',
    canvasComponent: SocialIconsBlock,
    propertiesComponent: SocialIconsProperties,
    isWrapper: false,
    canNest: true,
})

registerBlock({
    type: 'row',
    label: 'Row',
    icon: RowInsertIcon,
    category: 'layout',
    canvasComponent: RowBlock,
    propertiesComponent: RowProperties,
    isWrapper: true,
    canNest: false,
})

export function getBlockRegistration(type: BlockType): BlockRegistration | undefined {
    return blockRegistry.get(type)
}

export function getCanvasComponent(type: BlockType): Component | null {
    return blockRegistry.get(type)?.canvasComponent ?? null
}

export function getPropertiesComponent(type: BlockType): Component | null {
    return blockRegistry.get(type)?.propertiesComponent ?? null
}

export function getBlockIcon(type: BlockType): Component | null {
    return blockRegistry.get(type)?.icon ?? null
}

export function isWrapperBlock(type: BlockType): boolean {
    return blockRegistry.get(type)?.isWrapper ?? false
}

export function canBlockNest(type: BlockType): boolean {
    return blockRegistry.get(type)?.canNest ?? false
}

export function getAllBlockTypes(): BlockType[] {
    return Array.from(blockRegistry.keys())
}

export function getBlocksByCategory(category: 'content' | 'layout' | 'interactive'): BlockRegistration[] {
    return Array.from(blockRegistry.values()).filter((reg) => reg.category === category)
}

export function getAvailableBlocks(): Record<BlockType, BlockDefinition> {
    const result: Record<string, BlockDefinition> = {}
    for (const [type, reg] of blockRegistry) {
        result[type] = {
            type,
            label: reg.label,
            icon: reg.icon,
            defaultProps: getDefaultProps(type),
        }
    }
    return result as Record<BlockType, BlockDefinition>
}

export function getNestableBlocks(): Record<BlockType, BlockDefinition> {
    const result: Record<string, BlockDefinition> = {}
    for (const [type, reg] of blockRegistry) {
        if (reg.canNest) {
            result[type] = {
                type,
                label: reg.label,
                icon: reg.icon,
                defaultProps: getDefaultProps(type),
            }
        }
    }
    return result as Record<BlockType, BlockDefinition>
}
