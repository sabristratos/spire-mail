import { computed, triggerRef, type ComputedRef } from 'vue'
import type { EmailBlock, EmailSettings, BlockType } from '../../types/blocks'
import { state, defaultSettings, generateId } from './state'
import { getDefaultProps, findBlockRecursively } from './blockDefaults'
import { pushHistory } from './history'
import { deepClone } from '../../utils/clone'

/**
 * Initializes the editor store with blocks and settings.
 * Resets history and marks the document as clean.
 */
export function initializeStore(blocks: EmailBlock[] = [], settings?: EmailSettings): void {
    state.value.blocks = blocks
    state.value.settings = settings ?? { ...defaultSettings }
    state.value.selectedBlockId = null
    state.value.selectedColumnId = null
    state.value.selectedColumnRowId = null
    state.value.history = [deepClone(blocks)]
    state.value.historyIndex = 0
    state.value.isDirty = false
}

/**
 * Adds a new block to the document.
 */
export function addBlock(type: BlockType, index?: number): EmailBlock {
    const newBlock: EmailBlock = {
        id: generateId(),
        type,
        props: getDefaultProps(type),
    } as EmailBlock

    if (typeof index === 'number' && index >= 0) {
        state.value.blocks.splice(index, 0, newBlock)
    } else {
        state.value.blocks.push(newBlock)
    }

    state.value.selectedBlockId = newBlock.id
    state.value.isDirty = true
    pushHistory()

    return newBlock
}

/**
 * Updates properties of a specific block.
 * Searches both top-level blocks and blocks nested inside columns.
 */
export function updateBlock(id: string, props: Partial<Record<string, unknown>>): void {
    const block = findBlockRecursively(state.value.blocks, id)
    if (block) {
        block.props = { ...block.props, ...props }
        state.value.isDirty = true
        triggerRef(state)
    }
}

/**
 * Removes a block from the document.
 */
export function removeBlock(id: string): void {
    const index = state.value.blocks.findIndex((b) => b.id === id)
    if (index !== -1) {
        state.value.blocks.splice(index, 1)
        if (state.value.selectedBlockId === id) {
            state.value.selectedBlockId = null
        }
        state.value.isDirty = true
        pushHistory()
    }
}

/**
 * Moves a block from one position to another.
 */
export function moveBlock(fromIndex: number, toIndex: number): void {
    if (fromIndex === toIndex) return
    if (fromIndex < 0 || fromIndex >= state.value.blocks.length) return
    if (toIndex < 0 || toIndex >= state.value.blocks.length) return

    const [removed] = state.value.blocks.splice(fromIndex, 1)
    state.value.blocks.splice(toIndex, 0, removed)
    state.value.isDirty = true
    pushHistory()
}

/**
 * Duplicates a block and inserts the copy after the original.
 */
export function duplicateBlock(id: string): void {
    const index = state.value.blocks.findIndex((b) => b.id === id)
    if (index === -1) return

    const original = state.value.blocks[index]
    const copy: EmailBlock = {
        ...deepClone(original),
        id: generateId(),
    }

    state.value.blocks.splice(index + 1, 0, copy)
    state.value.selectedBlockId = copy.id
    state.value.isDirty = true
    pushHistory()
}

/**
 * Selects a block by ID.
 * Clears column selection when a block is selected.
 */
export function selectBlock(id: string | null): void {
    state.value.selectedBlockId = id
    if (id !== null) {
        state.value.selectedColumnId = null
        state.value.selectedColumnRowId = null
    }
}

/**
 * Selects a column by ID within a specific row.
 * Clears block selection when a column is selected.
 */
export function selectColumn(rowId: string | null, columnId: string | null): void {
    state.value.selectedColumnRowId = rowId
    state.value.selectedColumnId = columnId
    if (columnId !== null) {
        state.value.selectedBlockId = null
    }
}

/**
 * Clears the column selection.
 */
export function clearColumnSelection(): void {
    state.value.selectedColumnId = null
    state.value.selectedColumnRowId = null
}

/**
 * Sets the hovered block.
 */
export function hoverBlock(id: string | null): void {
    state.value.hoveredBlockId = id
}

/**
 * Retrieves a block by its ID.
 * Searches both top-level blocks and blocks nested inside columns.
 */
export function getBlockById(id: string): EmailBlock | undefined {
    return findBlockRecursively(state.value.blocks, id) ?? undefined
}

/**
 * Returns a computed reference to the currently selected block.
 */
export function getSelectedBlock(): ComputedRef<EmailBlock | null> {
    return computed(() => {
        if (!state.value.selectedBlockId) return null
        return findBlockRecursively(state.value.blocks, state.value.selectedBlockId)
    })
}

/**
 * Updates global email settings.
 */
export function updateSettings(settings: Partial<EmailSettings>): void {
    state.value.settings = { ...state.value.settings, ...settings }
    state.value.isDirty = true
}

/**
 * Exports the current document as a serializable object.
 */
export function exportDocument(): { version: string; blocks: EmailBlock[] } {
    return {
        version: '1.0',
        blocks: deepClone(state.value.blocks),
    }
}

/**
 * Marks the document as clean (no unsaved changes).
 */
export function markClean(): void {
    state.value.isDirty = false
}

/**
 * Sets the dragging state.
 */
export function setDragging(isDragging: boolean, blockType?: string | null): void {
    state.value.isDragging = isDragging
    state.value.draggedBlockType = blockType ?? null
    if (!isDragging) {
        state.value.dropTargetIndex = null
        state.value.dropTargetColumnId = null
        state.value.isDraggingInRow = false
    }
}

/**
 * Sets whether dragging is happening inside a row block.
 */
export function setDraggingInRow(isDraggingInRow: boolean): void {
    state.value.isDraggingInRow = isDraggingInRow
}

/**
 * Sets the current drop target index and optional column ID.
 */
export function setDropTarget(index: number | null, columnId: string | null = null): void {
    state.value.dropTargetIndex = index
    state.value.dropTargetColumnId = columnId
}

/**
 * Adds multiple blocks at the end of the document.
 * Used for adding template sections or loading full layouts.
 *
 * @param blocks - The blocks to add (should already have unique IDs)
 * @param replace - If true, replaces all existing blocks instead of appending
 */
export function addBlocksAtEnd(blocks: EmailBlock[], replace: boolean = false): void {
    if (replace) {
        state.value.blocks = [...blocks]
    } else {
        state.value.blocks.push(...blocks)
    }

    if (blocks.length > 0) {
        state.value.selectedBlockId = blocks[0].id
    }

    state.value.isDirty = true
    pushHistory()
}
