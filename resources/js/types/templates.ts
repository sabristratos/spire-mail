import type { EmailBlock } from './blocks'

/**
 * Template category types.
 */
export type TemplateCategory = 'section' | 'layout'

/**
 * Section template types for categorization.
 */
export type SectionType = 'header' | 'footer' | 'hero' | 'cta' | 'content' | 'social' | 'signature'

/**
 * Definition of an email template.
 */
export interface EmailTemplate {
    /** Unique identifier for the template */
    id: string
    /** Display name of the template */
    name: string
    /** Description of what the template contains */
    description?: string
    /** Whether this is a section or full layout template */
    category: TemplateCategory
    /** Type of section (only for section templates) */
    sectionType?: SectionType
    /** Preview thumbnail URL */
    thumbnail?: string
    /** The blocks that make up this template */
    blocks: EmailBlock[]
    /** Whether this is a built-in template */
    isBuiltIn: boolean
    /** Creation timestamp (for user templates) */
    createdAt?: string
}

/**
 * Template definition for the sidebar display.
 */
export interface TemplateDefinition {
    id: string
    name: string
    description?: string
    category: TemplateCategory
    sectionType?: SectionType
    thumbnail?: string
    isBuiltIn: boolean
}
