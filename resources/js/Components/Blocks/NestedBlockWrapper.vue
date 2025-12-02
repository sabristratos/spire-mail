<script setup lang="ts">
import { ref, computed } from 'vue'
import { Button, Icon } from '@sabrenski/spire-ui-vue'
import { ArrowUp01Icon, ArrowDown01Icon, Copy01Icon, Delete02Icon, DragDropVerticalIcon, CursorRectangleSelection02Icon } from '@hugeicons/core-free-icons'
import type { EmailBlock } from '../../types/blocks'
import { useBlockSelection } from '../../composables/useBlockSelection'
import { useDragDrop } from '../../composables/useDragDrop'

interface Props {
    /** The block being wrapped */
    block: EmailBlock
    /** Index of the block within its parent */
    index: number
    /** Total number of blocks in the parent */
    totalBlocks: number
    /** Type of parent container */
    parentType: 'column'
    /** ID of the parent block (columns or container block) */
    parentBlockId: string
    /** ID of the specific column (only for column parent type) */
    columnId?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
    moveUp: []
    moveDown: []
    duplicate: []
    delete: []
    select: []
}>()

const { isSelected, isHovered, hover, selectColumnById } = useBlockSelection()
const { createNestedBlockDragHandlers } = useDragDrop()

const isHoveredLocal = ref(false)

const selected = computed(() => isSelected(props.block.id))
const hovered = computed(() => isHovered(props.block.id) || isHoveredLocal.value)

const dragHandlers = computed(() =>
    createNestedBlockDragHandlers(props.parentType, props.parentBlockId, props.columnId, props.block.id, props.index)
)

const canMoveUp = computed(() => props.index > 0)
const canMoveDown = computed(() => props.index < props.totalBlocks - 1)

function handleClick(e: MouseEvent): void {
    e.stopPropagation()
    emit('select')
}

function handleMouseEnter(): void {
    isHoveredLocal.value = true
    hover(props.block.id)
}

function handleMouseLeave(): void {
    isHoveredLocal.value = false
    hover(null)
}

function handleMoveUp(e: MouseEvent): void {
    e.stopPropagation()
    emit('moveUp')
}

function handleMoveDown(e: MouseEvent): void {
    e.stopPropagation()
    emit('moveDown')
}

function handleDuplicate(e: MouseEvent): void {
    e.stopPropagation()
    emit('duplicate')
}

function handleDelete(e: MouseEvent): void {
    e.stopPropagation()
    emit('delete')
}

function handleSelectColumn(e: MouseEvent): void {
    e.stopPropagation()
    if (props.columnId) {
        selectColumnById(props.parentBlockId, props.columnId)
    }
}
</script>

<template>
    <div
        class="group/nested relative cursor-pointer transition-all duration-150"
        :class="[
            selected && 'ring-2 ring-primary ring-offset-1',
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
            <div class="group/tip relative">
                <Button
                    variant="ghost"
                    size="xs"
                    icon-only
                    :disabled="!canMoveUp"
                    aria-label="Move block up"
                    @click="handleMoveUp"
                >
                    <Icon :name="ArrowUp01Icon" size="xs" aria-hidden="true" />
                </Button>
                <span class="toolbar-tooltip">Move up</span>
            </div>
            <div class="group/tip relative">
                <Button
                    variant="ghost"
                    size="xs"
                    icon-only
                    :disabled="!canMoveDown"
                    aria-label="Move block down"
                    @click="handleMoveDown"
                >
                    <Icon :name="ArrowDown01Icon" size="xs" aria-hidden="true" />
                </Button>
                <span class="toolbar-tooltip">Move down</span>
            </div>
            <div class="group/tip relative">
                <Button variant="ghost" size="xs" icon-only aria-label="Duplicate block" @click="handleDuplicate">
                    <Icon :name="Copy01Icon" size="xs" aria-hidden="true" />
                </Button>
                <span class="toolbar-tooltip">Duplicate</span>
            </div>
            <div class="group/tip relative">
                <Button variant="danger" size="xs" icon-only aria-label="Delete block" @click="handleDelete">
                    <Icon :name="Delete02Icon" size="xs" aria-hidden="true" />
                </Button>
                <span class="toolbar-tooltip">Delete</span>
            </div>
            <div v-if="columnId" class="mx-0.5 h-4 w-px bg-border" />
            <div v-if="columnId" class="group/tip relative">
                <Button
                    variant="ghost"
                    size="xs"
                    icon-only
                    aria-label="Select parent column"
                    @click="handleSelectColumn"
                >
                    <Icon :name="CursorRectangleSelection02Icon" size="xs" aria-hidden="true" />
                </Button>
                <span class="toolbar-tooltip">Select column</span>
            </div>
        </div>

        <div
            v-bind="dragHandlers"
            role="button"
            tabindex="0"
            aria-label="Drag to reorder block"
            class="absolute left-0 top-1/2 z-10 flex -translate-x-full -translate-y-1/2 cursor-grab items-center justify-center rounded bg-elevated p-0.5 opacity-0 shadow-sm transition-opacity focus:opacity-100 group-hover/nested:opacity-100"
        >
            <Icon :name="DragDropVerticalIcon" size="sm" class="text-foreground-muted" aria-hidden="true" />
        </div>

        <slot />
    </div>
</template>

<style scoped>
.toolbar-tooltip {
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 6px;
    padding: 4px 8px;
    font-size: 12px;
    white-space: nowrap;
    border-radius: 4px;
    background-color: var(--color-foreground);
    color: var(--color-canvas);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.15s ease;
}

.group\/tip:hover .toolbar-tooltip {
    opacity: 1;
}
</style>
