import type { EmailBlock, EmailSettings, BlockDefinition } from './blocks'

/**
 * Internal state of the email editor.
 */
export interface EditorState {
    /** Array of email blocks in the document */
    blocks: EmailBlock[]
    /** Global email settings */
    settings: EmailSettings
    /** ID of the currently selected block */
    selectedBlockId: string | null
    /** ID of the block currently hovered over */
    hoveredBlockId: string | null
    /** Whether a drag operation is in progress */
    isDragging: boolean
    /** Type of block being dragged from the sidebar */
    draggedBlockType: string | null
    /** Index where the block will be dropped */
    dropTargetIndex: number | null
    /** History of block states for undo/redo */
    history: EmailBlock[][]
    /** Current position in the history stack */
    historyIndex: number
    /** Whether there are unsaved changes */
    isDirty: boolean
}

/**
 * Context provided to editor components.
 */
export interface EditorContext {
    /** Array of email blocks */
    blocks: EmailBlock[]
    /** Global email settings */
    settings: EmailSettings
    /** ID of the currently selected block */
    selectedBlockId: string | null
    /** Select a block by ID (pass null to deselect) */
    selectBlock: (id: string | null) => void
    /** Add a new block of the given type at the optional index */
    addBlock: (type: string, index?: number) => EmailBlock
    /** Update properties of a block */
    updateBlock: (id: string, props: Partial<Record<string, unknown>>) => void
    /** Remove a block by ID */
    removeBlock: (id: string) => void
    /** Move a block from one index to another */
    moveBlock: (fromIndex: number, toIndex: number) => void
    /** Duplicate a block by ID */
    duplicateBlock: (id: string) => void
}

/**
 * Email template data from the backend.
 */
export interface TemplateData {
    /** Unique identifier */
    id: number
    /** Template name */
    name: string
    /** URL-friendly slug */
    slug: string
    /** Email subject line */
    subject: string
    /** Optional description */
    description: string | null
    /** Template content with version and blocks */
    content: {
        version: string
        blocks: EmailBlock[]
    }
    /** Global email settings */
    settings: EmailSettings | null
    /** Preview text shown in email clients */
    preview_text: string | null
    /** Whether the template is active */
    is_active: boolean
    /** When the template was last sent */
    last_sent_at: string | null
    /** Creation timestamp */
    created_at: string
    /** Last update timestamp */
    updated_at: string
    /** Number of blocks in the template */
    block_count: number
}

/**
 * Props passed to the EmailEditor component.
 */
export interface EditorProps {
    /** Template data to edit (null for new template) */
    template: TemplateData | null
    /** Available block types from the registry */
    availableBlocks: Record<string, BlockDefinition>
    /** Show a back navigation link in the header */
    showBackLink?: boolean
    /** URL for the back link */
    backLinkHref?: string
}
