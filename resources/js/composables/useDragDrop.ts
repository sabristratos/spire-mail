/**
 * Drag and Drop Composable
 *
 * Main composable for managing drag and drop operations in the email editor.
 * Re-exports functionality from modular sub-composables for backward compatibility.
 *
 * @example
 * // Use the composable (backward compatible)
 * const { createPaletteDragHandlers, createDropZoneHandlers } = useDragDrop()
 *
 * // Or import specific functions directly for better tree-shaking
 * import { createPaletteDragHandlers } from '../composables/dragDrop/usePaletteDrag'
 */

export type {
    DragHandlers,
    DropZoneHandlers,
    BlockDragHandlers,
    ColumnDropZoneHandlers,
    NestedBlockDragContext,
} from './dragDrop'

import {
    draggedBlockId,
    draggedBlockIndex,
    draggedNestedBlock,
    createPaletteDragHandlers,
    createBlockDragHandlers,
    createNestedBlockDragHandlers,
    createDropZoneHandlers,
    createColumnDropZoneHandlers,
} from './dragDrop'

/**
 * Composable for managing drag and drop operations in the email editor.
 * Handles both palette-to-canvas drops (new blocks) and canvas reordering.
 */
export function useDragDrop() {
    return {
        draggedBlockId,
        draggedBlockIndex,
        draggedNestedBlock,
        createPaletteDragHandlers,
        createBlockDragHandlers,
        createNestedBlockDragHandlers,
        createDropZoneHandlers,
        createColumnDropZoneHandlers,
    }
}
