import { ref } from 'vue'
import type { NestedBlockDragContext } from './types'

/**
 * Shared drag state for coordinating between drag handlers.
 */
export const draggedBlockId = ref<string | null>(null)
export const draggedBlockIndex = ref<number | null>(null)
export const draggedNestedBlock = ref<NestedBlockDragContext | null>(null)
