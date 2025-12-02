<script setup lang="ts">
import { computed } from 'vue'
import { Button, Icon } from '@sabrenski/spire-ui-vue'
import { ArrowUp01Icon, ArrowDown01Icon, Copy01Icon, Delete02Icon, DragDropVerticalIcon } from '@hugeicons/core-free-icons'
import type { EmailBlock } from '../../types/blocks'
import { useBlockSelection } from '../../composables/useBlockSelection'
import { useDragDrop } from '../../composables/useDragDrop'
import { removeBlock, duplicateBlock, moveBlock, editorState } from '../../stores/editorStore'
import { getCanvasComponent, isWrapperBlock as checkIsWrapper } from '../../registry'

interface Props {
    block: EmailBlock
    index: number
}

const props = defineProps<Props>()

const { isSelected, isHovered, select, hover } = useBlockSelection()
const { createBlockDragHandlers } = useDragDrop()

const blockComponent = computed(() => getCanvasComponent(props.block.type))

const isWrapper = computed(() => checkIsWrapper(props.block.type))

const dragHandlers = computed(() => createBlockDragHandlers(props.block.id, props.index))

const selected = computed(() => isSelected(props.block.id))
const hovered = computed(() => isHovered(props.block.id))

function handleClick(e: MouseEvent): void {
    e.stopPropagation()
    select(props.block.id)
}

function handleMouseEnter(): void {
    hover(props.block.id)
}

function handleMouseLeave(): void {
    hover(null)
}

function handleMoveUp(): void {
    if (props.index > 0) {
        moveBlock(props.index, props.index - 1)
    }
}

function handleMoveDown(): void {
    if (props.index < editorState.value.blocks.length - 1) {
        moveBlock(props.index, props.index + 1)
    }
}

function handleDuplicate(): void {
    duplicateBlock(props.block.id)
}

function handleDelete(): void {
    removeBlock(props.block.id)
}
</script>

<template>
    <div
        class="group relative transition-all duration-150"
        :class="[
            selected && 'ring-2 ring-primary ring-offset-2',
            hovered && !selected && 'ring-1 ring-border',
        ]"
        @click="handleClick"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
    >
        <div
            v-if="selected"
            class="absolute -top-10 left-1/2 z-10 flex -translate-x-1/2 gap-1 rounded-lg border border-border bg-elevated p-1 shadow-lg"
            role="toolbar"
            aria-label="Block actions"
        >
            <Button variant="ghost" size="xs" icon-only :disabled="index === 0" aria-label="Move block up" @click.stop="handleMoveUp">
                <Icon :name="ArrowUp01Icon" size="xs" aria-hidden="true" />
            </Button>
            <Button
                variant="ghost"
                size="xs"
                icon-only
                :disabled="index === editorState.blocks.length - 1"
                aria-label="Move block down"
                @click.stop="handleMoveDown"
            >
                <Icon :name="ArrowDown01Icon" size="xs" aria-hidden="true" />
            </Button>
            <Button variant="ghost" size="xs" icon-only aria-label="Duplicate block" @click.stop="handleDuplicate">
                <Icon :name="Copy01Icon" size="xs" aria-hidden="true" />
            </Button>
            <Button variant="danger" size="xs" icon-only aria-label="Delete block" @click.stop="handleDelete">
                <Icon :name="Delete02Icon" size="xs" aria-hidden="true" />
            </Button>
        </div>

        <div
            v-bind="dragHandlers"
            role="button"
            tabindex="0"
            aria-label="Drag to reorder block"
            class="absolute left-0 top-1/2 z-10 -translate-x-full -translate-y-1/2 cursor-grab px-2 opacity-0 transition-opacity focus:opacity-100 group-hover:opacity-100"
        >
            <Icon :name="DragDropVerticalIcon" size="sm" class="text-foreground-muted" aria-hidden="true" />
        </div>

        <component
            v-if="blockComponent"
            :is="blockComponent"
            :block-props="block.props"
            :block-id="isWrapper ? block.id : undefined"
        />
    </div>
</template>
