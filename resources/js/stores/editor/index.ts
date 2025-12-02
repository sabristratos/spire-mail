/**
 * Editor Store Modules
 *
 * This module exports all editor store functionality organized into logical units.
 */

export { state as editorState, type EditorStoreState, defaultSettings, generateId } from './state'
export { getDefaultProps, getPresetWidths, createColumnsFromPreset, findBlockRecursively } from './blockDefaults'
export { pushHistory, undo, redo, canUndo, canRedo } from './history'
export {
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
} from './blockOps'
export {
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
} from './rowOps'
