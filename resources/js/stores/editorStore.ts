/**
 * Editor Store
 *
 * Re-exports all editor functionality from modular units.
 * This maintains backward compatibility while allowing the codebase
 * to use more focused imports from specific modules.
 *
 * @example
 * // Import everything (backward compatible)
 * import { addBlock, selectBlock, editorState } from '../stores/editorStore'
 *
 * // Or import from specific modules for better tree-shaking
 * import { addBlock, selectBlock } from '../stores/editor/blockOps'
 * import { editorState } from '../stores/editor/state'
 */

export {
    editorState,
    type EditorStoreState,
    defaultSettings,
    generateId,
    getDefaultProps,
    getPresetWidths,
    createColumnsFromPreset,
    findBlockRecursively,
    pushHistory,
    undo,
    redo,
    canUndo,
    canRedo,
    initializeStore,
    addBlock,
    addBlocksAtEnd,
    updateBlock,
    removeBlock,
    moveBlock,
    duplicateBlock,
    selectBlock,
    selectColumn,
    clearColumnSelection,
    hoverBlock,
    getBlockById,
    getSelectedBlock,
    updateSettings,
    exportDocument,
    markClean,
    setDragging,
    setDraggingInRow,
    setDropTarget,
    addBlockToColumn,
    removeBlockFromColumn,
    moveBlockWithinColumn,
    moveBlockBetweenColumns,
    updateColumnsPreset,
    updateColumnWidths,
    duplicateBlockInColumn,
    updateRowStyle,
    updateColumnStyle,
    moveBlockFromColumnToCanvas,
    moveBlockFromCanvasToColumn,
    moveBlockBetweenRows,
} from './editor'
