/**
 * Event handlers for draggable palette items.
 */
export interface DragHandlers {
    draggable: boolean
    onDragstart: (e: DragEvent) => void
    onDragend: (e: DragEvent) => void
}

/**
 * Event handlers for drop zone elements.
 */
export interface DropZoneHandlers {
    onDragover: (e: DragEvent) => void
    onDragleave: (e: DragEvent) => void
    onDrop: (e: DragEvent) => void
}

/**
 * Event handlers for draggable blocks in the canvas.
 */
export interface BlockDragHandlers {
    draggable: boolean
    onDragstart: (e: DragEvent) => void
    onDragend: (e: DragEvent) => void
}

/**
 * Event handlers for column drop zones within row blocks.
 */
export interface ColumnDropZoneHandlers {
    onDragover: (e: DragEvent) => void
    onDragleave: (e: DragEvent) => void
    onDrop: (e: DragEvent) => void
}

/**
 * Context for a nested block being dragged.
 */
export interface NestedBlockDragContext {
    parentType: 'column'
    parentBlockId: string
    columnId?: string
    blockId: string
    index: number
}
