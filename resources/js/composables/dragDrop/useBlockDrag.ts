import { setDragging } from '../../stores/editorStore'
import { draggedBlockId, draggedBlockIndex, draggedNestedBlock } from './state'
import type { BlockDragHandlers, NestedBlockDragContext } from './types'

/**
 * Creates drag handlers for blocks in the canvas for reordering.
 */
export function createBlockDragHandlers(blockId: string, index: number): BlockDragHandlers {
    return {
        draggable: true,
        onDragstart: (e: DragEvent) => {
            if (!e.dataTransfer) return
            e.dataTransfer.effectAllowed = 'move'
            e.dataTransfer.setData('application/x-block-id', blockId)
            draggedBlockId.value = blockId
            draggedBlockIndex.value = index
            setDragging(true)
        },
        onDragend: () => {
            draggedBlockId.value = null
            draggedBlockIndex.value = null
            setDragging(false)
        },
    }
}

/**
 * Creates drag handlers for nested blocks (inside row columns).
 */
export function createNestedBlockDragHandlers(
    parentType: 'column',
    parentBlockId: string,
    columnId: string | undefined,
    blockId: string,
    index: number
): BlockDragHandlers {
    return {
        draggable: true,
        onDragstart: (e: DragEvent) => {
            if (!e.dataTransfer) return
            e.stopPropagation()
            e.dataTransfer.effectAllowed = 'move'
            const context: NestedBlockDragContext = { parentType, parentBlockId, columnId, blockId, index }
            e.dataTransfer.setData('application/x-nested-block', JSON.stringify(context))
            draggedNestedBlock.value = context
            setDragging(true)
        },
        onDragend: () => {
            draggedNestedBlock.value = null
            setDragging(false)
        },
    }
}
