import type { BlockType } from '../../types/blocks'
import { setDragging } from '../../stores/editorStore'
import type { DragHandlers } from './types'

/**
 * Creates drag handlers for palette items in the sidebar.
 */
export function createPaletteDragHandlers(blockType: BlockType): DragHandlers {
    return {
        draggable: true,
        onDragstart: (e: DragEvent) => {
            if (!e.dataTransfer) return
            e.dataTransfer.effectAllowed = 'copy'
            e.dataTransfer.setData('application/x-block-type', blockType)
            setDragging(true, blockType)
        },
        onDragend: () => {
            setDragging(false)
        },
    }
}
