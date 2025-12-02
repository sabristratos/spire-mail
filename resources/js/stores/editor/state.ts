import { ref, type Ref } from 'vue'
import type { EmailBlock, EmailSettings } from '../../types/blocks'

/**
 * Internal state structure for the email editor.
 */
export interface EditorStoreState {
    blocks: EmailBlock[]
    settings: EmailSettings
    selectedBlockId: string | null
    hoveredBlockId: string | null
    selectedColumnId: string | null
    selectedColumnRowId: string | null
    isDragging: boolean
    isDraggingInRow: boolean
    draggedBlockType: string | null
    dropTargetIndex: number | null
    dropTargetColumnId: string | null
    history: EmailBlock[][]
    historyIndex: number
    isDirty: boolean
}

export const defaultSettings: EmailSettings = {
    fontFamily: 'Arial, sans-serif',
    backgroundColor: '#f5f5f5',
    contentBackgroundColor: '#ffffff',
    contentWidth: 700,
}

export const state: Ref<EditorStoreState> = ref<EditorStoreState>({
    blocks: [],
    settings: { ...defaultSettings },
    selectedBlockId: null,
    hoveredBlockId: null,
    selectedColumnId: null,
    selectedColumnRowId: null,
    isDragging: false,
    isDraggingInRow: false,
    draggedBlockType: null,
    dropTargetIndex: null,
    dropTargetColumnId: null,
    history: [],
    historyIndex: -1,
    isDirty: false,
})

/**
 * Generates a UUID for a new block.
 */
export function generateId(): string {
    return crypto.randomUUID()
}
