import type { BlockType, ContentBlockType } from '../../types/blocks'
import {
    setDragging,
    setDropTarget,
    editorState,
    addBlockToColumn,
    moveBlockWithinColumn,
    moveBlockBetweenColumns,
    moveBlockFromCanvasToColumn,
    moveBlockBetweenRows,
} from '../../stores/editorStore'
import { draggedBlockId, draggedBlockIndex, draggedNestedBlock } from './state'
import type { ColumnDropZoneHandlers, NestedBlockDragContext } from './types'

/**
 * Creates drop zone handlers for areas within columns.
 * Handles: new block drops from palette, nested block reordering,
 * canvas blocks moving into columns, and cross-row column moves.
 */
export function createColumnDropZoneHandlers(
    rowBlockId: string,
    columnId: string,
    targetIndex: number
): ColumnDropZoneHandlers {
    return {
        onDragover: (e: DragEvent) => {
            e.preventDefault()
            e.stopPropagation()
            if (!e.dataTransfer) return

            const hasBlockType = e.dataTransfer.types.includes('application/x-block-type')
            const hasNestedBlock = e.dataTransfer.types.includes('application/x-nested-block')
            const hasBlockId = e.dataTransfer.types.includes('application/x-block-id')

            if (hasBlockType || hasNestedBlock || hasBlockId) {
                e.dataTransfer.dropEffect = hasBlockType ? 'copy' : 'move'
                setDropTarget(targetIndex, columnId)
            }
        },
        onDragleave: (e: DragEvent) => {
            e.stopPropagation()
            const relatedTarget = e.relatedTarget as HTMLElement | null
            const currentTarget = e.currentTarget as HTMLElement

            if (!relatedTarget || !currentTarget.contains(relatedTarget)) {
                if (
                    editorState.value.dropTargetIndex === targetIndex &&
                    editorState.value.dropTargetColumnId === columnId
                ) {
                    setDropTarget(null, null)
                }
            }
        },
        onDrop: (e: DragEvent) => {
            e.preventDefault()
            e.stopPropagation()
            if (!e.dataTransfer) return

            const blockType = e.dataTransfer.getData('application/x-block-type') as BlockType
            const nestedBlockData = e.dataTransfer.getData('application/x-nested-block')
            const canvasBlockId = e.dataTransfer.getData('application/x-block-id')

            if (blockType && blockType !== 'row') {
                addBlockToColumn(rowBlockId, columnId, blockType as ContentBlockType, targetIndex)
            } else if (canvasBlockId && draggedBlockIndex.value !== null) {
                moveBlockFromCanvasToColumn(canvasBlockId, rowBlockId, columnId, targetIndex)
            } else if (nestedBlockData) {
                const context: NestedBlockDragContext = JSON.parse(nestedBlockData)

                if (context.parentType === 'column' && context.columnId) {
                    if (context.parentBlockId === rowBlockId) {
                        if (context.columnId === columnId) {
                            let adjustedTarget = targetIndex
                            if (context.index < targetIndex) {
                                adjustedTarget--
                            }
                            moveBlockWithinColumn(rowBlockId, columnId, context.index, adjustedTarget)
                        } else {
                            moveBlockBetweenColumns(rowBlockId, context.columnId, columnId, context.blockId, targetIndex)
                        }
                    } else {
                        moveBlockBetweenRows(
                            context.parentBlockId,
                            context.columnId,
                            rowBlockId,
                            columnId,
                            context.blockId,
                            targetIndex
                        )
                    }
                }
            }

            setDragging(false)
            setDropTarget(null, null)
            draggedBlockId.value = null
            draggedBlockIndex.value = null
            draggedNestedBlock.value = null
        },
    }
}
