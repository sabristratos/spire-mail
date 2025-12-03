<script setup lang="ts">
import { computed } from 'vue'
import { Icon } from '@sabrenski/spire-ui-vue'
import { Add01Icon } from '@hugeicons/core-free-icons'
import type { RowBlockProps, ContentBlock } from '../../types/blocks'
import TextBlock from './TextBlock.vue'
import HeadingBlock from './HeadingBlock.vue'
import ImageBlock from './ImageBlock.vue'
import ButtonBlock from './ButtonBlock.vue'
import DividerBlock from './DividerBlock.vue'
import SpacerBlock from './SpacerBlock.vue'
import HtmlBlock from './HtmlBlock.vue'
import VideoBlock from './VideoBlock.vue'
import SocialIconsBlock from './SocialIconsBlock.vue'
import NestedBlockWrapper from './NestedBlockWrapper.vue'
import ColumnWrapper from './ColumnWrapper.vue'
import { useDragDrop } from '../../composables/useDragDrop'
import { useBlockSelection } from '../../composables/useBlockSelection'
import {
    editorState,
    removeBlockFromColumn,
    moveBlockWithinColumn,
    duplicateBlockInColumn,
    setDraggingInRow,
} from '../../stores/editorStore'

interface Props {
    blockProps: RowBlockProps
    blockId: string
}

const props = defineProps<Props>()

const { createColumnDropZoneHandlers } = useDragDrop()
const { select } = useBlockSelection()

const columnGap = computed(() => props.blockProps.gap ?? 10)

const rowStyles = computed(() => {
    const p = props.blockProps
    const style = p.style ?? {}
    const padding = style.padding ?? { top: 0, right: 0, bottom: 0, left: 0 }

    return {
        padding: `${padding.top ?? 0}px ${padding.right ?? 0}px ${padding.bottom ?? 0}px ${padding.left ?? 0}px`,
        backgroundColor: style.backgroundColor,
        backgroundImage: style.backgroundImage ? `url(${style.backgroundImage})` : undefined,
        backgroundSize: style.backgroundSize,
        backgroundPosition: style.backgroundPosition,
        borderRadius: style.borderRadius ? `${style.borderRadius}px` : undefined,
        textAlign: style.textAlign,
        borderSpacing: `${columnGap.value}px 0`,
    }
})

function getBlockComponent(type: string) {
    switch (type) {
        case 'text':
            return TextBlock
        case 'heading':
            return HeadingBlock
        case 'image':
            return ImageBlock
        case 'button':
            return ButtonBlock
        case 'divider':
            return DividerBlock
        case 'spacer':
            return SpacerBlock
        case 'html':
            return HtmlBlock
        case 'video':
            return VideoBlock
        case 'social':
            return SocialIconsBlock
        default:
            return null
    }
}

function getColumnStyles(column: { style?: { backgroundColor?: string; padding?: { top?: number; right?: number; bottom?: number; left?: number }; borderRadius?: number; verticalAlign?: string } }) {
    const style = column.style ?? {}
    const padding = style.padding ?? {}

    return {
        backgroundColor: style.backgroundColor,
        padding: padding.top || padding.right || padding.bottom || padding.left
            ? `${padding.top ?? 0}px ${padding.right ?? 0}px ${padding.bottom ?? 0}px ${padding.left ?? 0}px`
            : undefined,
        borderRadius: style.borderRadius ? `${style.borderRadius}px` : undefined,
        justifyContent: style.verticalAlign === 'middle' ? 'center' : style.verticalAlign === 'bottom' ? 'flex-end' : 'flex-start',
    }
}

function handleSelect(blockId: string): void {
    select(blockId)
}

function handleMoveUp(columnId: string, index: number): void {
    if (index > 0) {
        moveBlockWithinColumn(props.blockId, columnId, index, index - 1)
    }
}

function handleMoveDown(columnId: string, index: number, totalBlocks: number): void {
    if (index < totalBlocks - 1) {
        moveBlockWithinColumn(props.blockId, columnId, index, index + 1)
    }
}

function handleDuplicate(columnId: string, blockId: string): void {
    duplicateBlockInColumn(props.blockId, columnId, blockId)
}

function handleDelete(columnId: string, blockId: string): void {
    removeBlockFromColumn(props.blockId, columnId, blockId)
}

function handleRowDragEnter(e: DragEvent): void {
    if (editorState.value.isDragging) {
        setDraggingInRow(true)
    }
}

function handleRowDragLeave(e: DragEvent): void {
    const relatedTarget = e.relatedTarget as HTMLElement | null
    const currentTarget = e.currentTarget as HTMLElement
    if (!relatedTarget || !currentTarget.contains(relatedTarget)) {
        setDraggingInRow(false)
    }
}
</script>

<template>
    <div
        class="email-row"
        :class="blockProps.stackOnMobile !== false ? 'email-row--stacked' : 'email-row--no-stack'"
        :style="rowStyles"
        @dragenter="handleRowDragEnter"
        @dragleave="handleRowDragLeave"
    >
        <ColumnWrapper
            v-for="(column, colIndex) in blockProps.columns"
            :key="column.id"
            :column="column"
            :row-id="blockId"
            :index="colIndex"
            :style="{ width: column.width, ...getColumnStyles(column) }"
        >
            <div
                v-if="column.blocks.length === 0"
                v-bind="createColumnDropZoneHandlers(blockId, column.id, 0)"
                class="flex min-h-[80px] h-full items-center justify-center p-2 transition-all"
                :class="[
                    editorState.dropTargetIndex === 0 &&
                    editorState.dropTargetColumnId === column.id &&
                    editorState.isDragging
                        ? 'bg-primary/20'
                        : 'opacity-50'
                ]"
            >
                <Icon :name="Add01Icon" size="sm" class="text-foreground-muted" aria-hidden="true" />
            </div>

            <div v-else class="flex flex-col p-2">
                <div
                    v-bind="createColumnDropZoneHandlers(blockId, column.id, 0)"
                    class="transition-all"
                    :class="[
                        editorState.isDragging ? 'h-6' : 'h-1',
                        editorState.dropTargetIndex === 0 &&
                        editorState.dropTargetColumnId === column.id &&
                        editorState.isDragging
                            ? 'bg-primary/20'
                            : editorState.isDragging ? 'bg-border/30' : '',
                    ]"
                />

                <template v-for="(block, index) in column.blocks" :key="block.id">
                    <NestedBlockWrapper
                        :block="block"
                        :index="index"
                        :total-blocks="column.blocks.length"
                        parent-type="column"
                        :parent-block-id="blockId"
                        :column-id="column.id"
                        @move-up="handleMoveUp(column.id, index)"
                        @move-down="handleMoveDown(column.id, index, column.blocks.length)"
                        @duplicate="handleDuplicate(column.id, block.id)"
                        @delete="handleDelete(column.id, block.id)"
                        @select="handleSelect(block.id)"
                    >
                        <component
                            :is="getBlockComponent(block.type)"
                            v-if="getBlockComponent(block.type)"
                            :block-props="(block.props as any)"
                        />
                    </NestedBlockWrapper>

                    <div
                        v-bind="createColumnDropZoneHandlers(blockId, column.id, index + 1)"
                        class="transition-all"
                        :class="[
                            editorState.isDragging ? 'h-6' : 'h-1',
                            editorState.dropTargetIndex === index + 1 &&
                            editorState.dropTargetColumnId === column.id &&
                            editorState.isDragging
                                ? 'bg-primary/20'
                                : editorState.isDragging ? 'bg-border/30' : '',
                        ]"
                    />
                </template>
            </div>
        </ColumnWrapper>
    </div>
</template>

<style scoped>
.email-row {
    min-height: 40px;
}
</style>
