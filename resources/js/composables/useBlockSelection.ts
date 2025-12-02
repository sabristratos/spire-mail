import { computed } from 'vue'
import { selectBlock, selectColumn, clearColumnSelection, hoverBlock, getBlockById, editorState } from '../stores/editorStore'
import type { EmailBlock, ColumnData, RowBlock } from '../types/blocks'

/**
 * Composable for managing block selection and hover states in the editor.
 * Provides reactive state and methods for selecting/hovering blocks.
 */
export function useBlockSelection() {
    /** Reactive ID of the currently selected block */
    const selectedBlockId = computed(() => editorState.value.selectedBlockId)

    /** Reactive ID of the currently hovered block */
    const hoveredBlockId = computed(() => editorState.value.hoveredBlockId)

    /** Reactive ID of the currently selected column */
    const selectedColumnId = computed(() => editorState.value.selectedColumnId)

    /** Reactive ID of the row containing the selected column */
    const selectedColumnRowId = computed(() => editorState.value.selectedColumnRowId)

    /** Reactive reference to the full selected block object */
    const selectedBlock = computed<EmailBlock | null>(() => {
        if (!selectedBlockId.value) return null
        return getBlockById(selectedBlockId.value) ?? null
    })

    /** Reactive reference to the selected column data */
    const selectedColumn = computed<ColumnData | null>(() => {
        if (!selectedColumnId.value || !selectedColumnRowId.value) return null
        const rowBlock = getBlockById(selectedColumnRowId.value) as RowBlock | undefined
        if (!rowBlock || rowBlock.type !== 'row') return null
        return rowBlock.props.columns.find((col) => col.id === selectedColumnId.value) ?? null
    })

    /** Index of the selected column within its row */
    const selectedColumnIndex = computed<number>(() => {
        if (!selectedColumnId.value || !selectedColumnRowId.value) return -1
        const rowBlock = getBlockById(selectedColumnRowId.value) as RowBlock | undefined
        if (!rowBlock || rowBlock.type !== 'row') return -1
        return rowBlock.props.columns.findIndex((col) => col.id === selectedColumnId.value)
    })

    /**
     * Checks if a block is currently selected.
     * @param blockId - The ID of the block to check
     */
    function isSelected(blockId: string): boolean {
        return selectedBlockId.value === blockId
    }

    /**
     * Checks if a block is currently hovered.
     * @param blockId - The ID of the block to check
     */
    function isHovered(blockId: string): boolean {
        return hoveredBlockId.value === blockId
    }

    /**
     * Selects a block by ID.
     * @param blockId - The ID of the block to select, or null to deselect
     */
    function select(blockId: string | null): void {
        selectBlock(blockId)
    }

    /**
     * Sets the hover state for a block.
     * @param blockId - The ID of the block to hover, or null to clear hover
     */
    function hover(blockId: string | null): void {
        hoverBlock(blockId)
    }

    /**
     * Clears the current block selection.
     */
    function clearSelection(): void {
        selectBlock(null)
    }

    /**
     * Checks if a column is currently selected.
     * @param columnId - The ID of the column to check
     */
    function isColumnSelected(columnId: string): boolean {
        return selectedColumnId.value === columnId
    }

    /**
     * Selects a column by row ID and column ID.
     * @param rowId - The ID of the parent row block
     * @param columnId - The ID of the column to select
     */
    function selectColumnById(rowId: string, columnId: string): void {
        selectColumn(rowId, columnId)
    }

    /**
     * Clears all selection (both block and column).
     */
    function clearAllSelection(): void {
        selectBlock(null)
        clearColumnSelection()
    }

    return {
        selectedBlockId,
        hoveredBlockId,
        selectedColumnId,
        selectedColumnRowId,
        selectedBlock,
        selectedColumn,
        selectedColumnIndex,
        isSelected,
        isHovered,
        isColumnSelected,
        select,
        selectColumnById,
        hover,
        clearSelection,
        clearAllSelection,
    }
}
