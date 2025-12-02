import type { BlockType } from '../../types/blocks'
import { addBlock, setDragging, setDropTarget, moveBlock, editorState, moveBlockFromColumnToCanvas } from '../../stores/editorStore'
import { draggedBlockId, draggedBlockIndex, draggedNestedBlock } from './state'
import type { DropZoneHandlers, NestedBlockDragContext } from './types'

/**
 * Creates drop zone handlers for areas between blocks on the main canvas.
 * Handles: new blocks from palette, canvas block reordering, and nested blocks from columns.
 */
export function createDropZoneHandlers(targetIndex: number): DropZoneHandlers {
    return {
        onDragover: (e: DragEvent) => {
            e.preventDefault()
            if (!e.dataTransfer) return

            const hasBlockType = e.dataTransfer.types.includes('application/x-block-type')
            const hasBlockId = e.dataTransfer.types.includes('application/x-block-id')
            const hasNestedBlock = e.dataTransfer.types.includes('application/x-nested-block')

            if (hasBlockType || hasBlockId || hasNestedBlock) {
                e.dataTransfer.dropEffect = hasBlockType ? 'copy' : 'move'
                setDropTarget(targetIndex)
            }
        },
        onDragleave: (e: DragEvent) => {
            const relatedTarget = e.relatedTarget as HTMLElement | null
            const currentTarget = e.currentTarget as HTMLElement

            if (!relatedTarget || !currentTarget.contains(relatedTarget)) {
                if (editorState.value.dropTargetIndex === targetIndex) {
                    setDropTarget(null)
                }
            }
        },
        onDrop: (e: DragEvent) => {
            e.preventDefault()
            if (!e.dataTransfer) return

            const blockType = e.dataTransfer.getData('application/x-block-type') as BlockType
            const blockId = e.dataTransfer.getData('application/x-block-id')
            const nestedBlockData = e.dataTransfer.getData('application/x-nested-block')

            if (blockType) {
                addBlock(blockType, targetIndex)
            } else if (blockId && draggedBlockIndex.value !== null) {
                const fromIndex = draggedBlockIndex.value
                let toIndex = targetIndex

                if (fromIndex < toIndex) {
                    toIndex--
                }

                moveBlock(fromIndex, toIndex)
            } else if (nestedBlockData) {
                const context: NestedBlockDragContext = JSON.parse(nestedBlockData)

                if (context.parentType === 'column' && context.columnId) {
                    moveBlockFromColumnToCanvas(
                        context.parentBlockId,
                        context.columnId,
                        context.blockId,
                        targetIndex
                    )
                }
            }

            setDragging(false)
            setDropTarget(null)
            draggedBlockId.value = null
            draggedBlockIndex.value = null
            draggedNestedBlock.value = null
        },
    }
}
