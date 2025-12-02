import { triggerRef } from 'vue'
import type {
    RowBlock,
    ContentBlock,
    ContentBlockType,
    ColumnPreset,
    ColumnData,
    RowStyle,
    ColumnStyle,
} from '../../types/blocks'
import { state, generateId } from './state'
import { getDefaultProps, getPresetWidths } from './blockDefaults'
import { pushHistory } from './history'
import { deepClone } from '../../utils/clone'

/**
 * Finds a row block and returns it with its index.
 */
function findRowBlock(rowBlockId: string): { block: RowBlock; index: number } | null {
    const index = state.value.blocks.findIndex((b) => b.id === rowBlockId)
    if (index === -1) return null
    const block = state.value.blocks[index]
    if (block.type !== 'row') return null
    return { block: block as RowBlock, index }
}

/**
 * Adds a content block to a specific column within a row block.
 */
export function addBlockToColumn(
    rowBlockId: string,
    columnId: string,
    blockType: ContentBlockType,
    index?: number
): ContentBlock | null {
    const found = findRowBlock(rowBlockId)
    if (!found) return null

    const column = found.block.props.columns.find((c) => c.id === columnId)
    if (!column) return null

    const newBlock: ContentBlock = {
        id: generateId(),
        type: blockType,
        props: getDefaultProps(blockType),
    } as ContentBlock

    if (typeof index === 'number' && index >= 0) {
        column.blocks.splice(index, 0, newBlock)
    } else {
        column.blocks.push(newBlock)
    }

    state.value.selectedBlockId = newBlock.id
    state.value.isDirty = true
    triggerRef(state)
    pushHistory()

    return newBlock
}

/**
 * Removes a content block from a column.
 */
export function removeBlockFromColumn(rowBlockId: string, columnId: string, blockId: string): void {
    const found = findRowBlock(rowBlockId)
    if (!found) return

    const column = found.block.props.columns.find((c) => c.id === columnId)
    if (!column) return

    const blockIndex = column.blocks.findIndex((b) => b.id === blockId)
    if (blockIndex === -1) return

    column.blocks.splice(blockIndex, 1)

    if (state.value.selectedBlockId === blockId) {
        state.value.selectedBlockId = null
    }

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Moves a block within the same column.
 */
export function moveBlockWithinColumn(
    rowBlockId: string,
    columnId: string,
    fromIndex: number,
    toIndex: number
): void {
    if (fromIndex === toIndex) return

    const found = findRowBlock(rowBlockId)
    if (!found) return

    const column = found.block.props.columns.find((c) => c.id === columnId)
    if (!column) return

    if (fromIndex < 0 || fromIndex >= column.blocks.length) return
    if (toIndex < 0 || toIndex >= column.blocks.length) return

    const [removed] = column.blocks.splice(fromIndex, 1)
    column.blocks.splice(toIndex, 0, removed)

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Moves a block from one column to another.
 */
export function moveBlockBetweenColumns(
    rowBlockId: string,
    fromColumnId: string,
    toColumnId: string,
    blockId: string,
    targetIndex: number
): void {
    const found = findRowBlock(rowBlockId)
    if (!found) return

    const fromColumn = found.block.props.columns.find((c) => c.id === fromColumnId)
    const toColumn = found.block.props.columns.find((c) => c.id === toColumnId)
    if (!fromColumn || !toColumn) return

    const blockIndex = fromColumn.blocks.findIndex((b) => b.id === blockId)
    if (blockIndex === -1) return

    const [removed] = fromColumn.blocks.splice(blockIndex, 1)
    toColumn.blocks.splice(targetIndex, 0, removed)

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Updates the preset of a row block and adjusts columns accordingly.
 */
export function updateColumnsPreset(blockId: string, preset: ColumnPreset): void {
    const found = findRowBlock(blockId)
    if (!found) return

    const widths = getPresetWidths(preset)
    const currentColumns = found.block.props.columns
    const newColumns: ColumnData[] = []

    for (let i = 0; i < widths.length; i++) {
        if (i < currentColumns.length) {
            newColumns.push({
                ...currentColumns[i],
                width: widths[i],
            })
        } else {
            newColumns.push({
                id: generateId(),
                width: widths[i],
                blocks: [],
            })
        }
    }

    if (currentColumns.length > widths.length) {
        const lastColumn = newColumns[newColumns.length - 1]
        for (let i = widths.length; i < currentColumns.length; i++) {
            lastColumn.blocks.push(...currentColumns[i].blocks)
        }
    }

    found.block.props.preset = preset
    found.block.props.columns = newColumns

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Updates custom column widths for a row block.
 */
export function updateColumnWidths(blockId: string, widths: string[]): void {
    const found = findRowBlock(blockId)
    if (!found) return

    const columns = found.block.props.columns
    widths.forEach((width, index) => {
        if (index < columns.length) {
            columns[index].width = width
        }
    })

    found.block.props.preset = 'custom'
    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Duplicates a content block within a column.
 */
export function duplicateBlockInColumn(
    rowBlockId: string,
    columnId: string,
    blockId: string
): ContentBlock | null {
    const found = findRowBlock(rowBlockId)
    if (!found) return null

    const column = found.block.props.columns.find((c) => c.id === columnId)
    if (!column) return null

    const blockIndex = column.blocks.findIndex((b) => b.id === blockId)
    if (blockIndex === -1) return null

    const original = column.blocks[blockIndex]
    const copy: ContentBlock = {
        ...deepClone(original),
        id: generateId(),
    }

    column.blocks.splice(blockIndex + 1, 0, copy)
    state.value.selectedBlockId = copy.id
    state.value.isDirty = true
    triggerRef(state)
    pushHistory()

    return copy
}

/**
 * Updates the row-level style properties.
 */
export function updateRowStyle(rowBlockId: string, styleUpdates: Partial<RowStyle>): void {
    const found = findRowBlock(rowBlockId)
    if (!found) return

    found.block.props.style = {
        ...(found.block.props.style ?? {}),
        ...styleUpdates,
    }

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Updates the style properties for a specific column.
 */
export function updateColumnStyle(
    rowBlockId: string,
    columnIndex: number,
    styleUpdates: Partial<ColumnStyle>
): void {
    const found = findRowBlock(rowBlockId)
    if (!found) return

    const column = found.block.props.columns[columnIndex]
    if (!column) return

    column.style = {
        ...(column.style ?? {}),
        ...styleUpdates,
    }

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Moves a block from a column to become a top-level canvas block.
 */
export function moveBlockFromColumnToCanvas(
    sourceRowId: string,
    sourceColumnId: string,
    blockId: string,
    targetCanvasIndex: number
): void {
    const found = findRowBlock(sourceRowId)
    if (!found) return

    const sourceColumn = found.block.props.columns.find((c) => c.id === sourceColumnId)
    if (!sourceColumn) return

    const blockIndex = sourceColumn.blocks.findIndex((b) => b.id === blockId)
    if (blockIndex === -1) return

    const [block] = sourceColumn.blocks.splice(blockIndex, 1)

    state.value.blocks.splice(targetCanvasIndex, 0, block)
    state.value.selectedBlockId = block.id

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Moves a top-level canvas block into a column.
 */
export function moveBlockFromCanvasToColumn(
    canvasBlockId: string,
    targetRowId: string,
    targetColumnId: string,
    targetIndex: number
): void {
    const canvasIndex = state.value.blocks.findIndex((b) => b.id === canvasBlockId)
    if (canvasIndex === -1) return

    const block = state.value.blocks[canvasIndex]

    if (block.type === 'row') {
        return
    }

    const found = findRowBlock(targetRowId)
    if (!found) return

    const targetColumn = found.block.props.columns.find((c) => c.id === targetColumnId)
    if (!targetColumn) return

    state.value.blocks.splice(canvasIndex, 1)

    targetColumn.blocks.splice(targetIndex, 0, block as ContentBlock)
    state.value.selectedBlockId = block.id

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}

/**
 * Moves a block from one column to another column in a different row.
 */
export function moveBlockBetweenRows(
    sourceRowId: string,
    sourceColumnId: string,
    targetRowId: string,
    targetColumnId: string,
    blockId: string,
    targetIndex: number
): void {
    const sourceFound = findRowBlock(sourceRowId)
    const targetFound = findRowBlock(targetRowId)
    if (!sourceFound || !targetFound) return

    const sourceColumn = sourceFound.block.props.columns.find((c) => c.id === sourceColumnId)
    const targetColumn = targetFound.block.props.columns.find((c) => c.id === targetColumnId)
    if (!sourceColumn || !targetColumn) return

    const blockIndex = sourceColumn.blocks.findIndex((b) => b.id === blockId)
    if (blockIndex === -1) return

    const [block] = sourceColumn.blocks.splice(blockIndex, 1)
    targetColumn.blocks.splice(targetIndex, 0, block)

    state.value.isDirty = true
    triggerRef(state)
    pushHistory()
}
