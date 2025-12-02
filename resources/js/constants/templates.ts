import type { EmailTemplate, SectionType } from '../types/templates'
import type { EmailBlock, RowBlock, ContentBlock } from '../types/blocks'

/**
 * Generate a unique ID for blocks.
 * Uses crypto.randomUUID when available, with a fallback for unsupported environments.
 */
function id(): string {
    if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
        return crypto.randomUUID()
    }
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, (c) => {
        const r = (Math.random() * 16) | 0
        const v = c === 'x' ? r : (r & 0x3) | 0x8
        return v.toString(16)
    })
}

/**
 * Creates a deep clone of blocks with fresh IDs.
 */
export function cloneBlocksWithNewIds(blocks: EmailBlock[]): EmailBlock[] {
    return blocks.map((block) => cloneBlockWithNewId(block))
}

function cloneBlockWithNewId(block: EmailBlock): EmailBlock {
    if (block.type === 'row') {
        const rowBlock = block as RowBlock
        return {
            ...rowBlock,
            id: id(),
            props: {
                ...rowBlock.props,
                columns: rowBlock.props.columns.map((col) => ({
                    ...col,
                    id: id(),
                    blocks: col.blocks.map((b) => ({
                        ...b,
                        id: id(),
                    })),
                })),
            },
        }
    }
    return { ...block, id: id() }
}

/**
 * Section Templates - Draggable reusable sections
 */
export const SECTION_TEMPLATES: EmailTemplate[] = [
    {
        id: 'header-basic',
        name: 'Basic Header',
        description: 'Logo with navigation links',
        category: 'section',
        sectionType: 'header',
        isBuiltIn: true,
        blocks: [
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '2-left-heavy',
                    gap: 10,
                    stackOnMobile: true,
                    columns: [
                        {
                            id: 'temp',
                            width: '30%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'image',
                                    props: {
                                        src: 'https://placehold.co/150x50/3b82f6/ffffff?text=LOGO',
                                        alt: 'Company Logo',
                                        width: '150px',
                                        align: 'left',
                                        padding: { top: 10, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                            ],
                        },
                        {
                            id: 'temp',
                            width: '70%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: '<a href="#">Home</a> &nbsp;|&nbsp; <a href="#">About</a> &nbsp;|&nbsp; <a href="#">Contact</a>',
                                        variant: 'paragraph',
                                        align: 'right',
                                        padding: { top: 20, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        backgroundColor: '#ffffff',
                        padding: { top: 0, right: 20, bottom: 0, left: 20 },
                    },
                },
            },
        ],
    },
    {
        id: 'hero-centered',
        name: 'Centered Hero',
        description: 'Eye-catching hero section with image and CTA',
        category: 'section',
        sectionType: 'hero',
        isBuiltIn: true,
        blocks: [
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '1-column',
                    columns: [
                        {
                            id: 'temp',
                            width: '100%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'image',
                                    props: {
                                        src: 'https://placehold.co/600x300/3b82f6/ffffff?text=Hero+Image',
                                        alt: 'Hero Image',
                                        width: '100%',
                                        align: 'center',
                                        borderRadius: 8,
                                        padding: { top: 0, right: 0, bottom: 20, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: {
                                        content: 'Welcome to Our Newsletter',
                                        level: 'h1',
                                        align: 'center',
                                        padding: { top: 0, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: 'Stay updated with the latest news, tips, and exclusive offers delivered straight to your inbox.',
                                        variant: 'paragraph',
                                        align: 'center',
                                        padding: { top: 0, right: 20, bottom: 20, left: 20 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'button',
                                    props: {
                                        text: 'Learn More',
                                        href: '#',
                                        variant: 'primary',
                                        size: 'lg',
                                        align: 'center',
                                        padding: { top: 0, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        padding: { top: 30, right: 20, bottom: 30, left: 20 },
                    },
                },
            },
        ],
    },
    {
        id: 'content-two-column',
        name: 'Two Column Content',
        description: 'Image with text side by side',
        category: 'section',
        sectionType: 'content',
        isBuiltIn: true,
        blocks: [
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '2-equal',
                    gap: 20,
                    stackOnMobile: true,
                    columns: [
                        {
                            id: 'temp',
                            width: '50%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'image',
                                    props: {
                                        src: 'https://placehold.co/300x200/e2e8f0/64748b?text=Image',
                                        alt: 'Content Image',
                                        width: '100%',
                                        align: 'center',
                                        borderRadius: 8,
                                    },
                                },
                            ],
                        },
                        {
                            id: 'temp',
                            width: '50%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: {
                                        content: 'Featured Content',
                                        level: 'h2',
                                        align: 'left',
                                        padding: { top: 0, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                                        variant: 'paragraph',
                                        align: 'left',
                                        padding: { top: 0, right: 0, bottom: 15, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'button',
                                    props: {
                                        text: 'Read More',
                                        href: '#',
                                        variant: 'outline',
                                        size: 'md',
                                        align: 'left',
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        padding: { top: 20, right: 20, bottom: 20, left: 20 },
                    },
                },
            },
        ],
    },
    {
        id: 'cta-centered',
        name: 'Call to Action',
        description: 'Centered CTA with heading and button',
        category: 'section',
        sectionType: 'cta',
        isBuiltIn: true,
        blocks: [
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '1-column',
                    columns: [
                        {
                            id: 'temp',
                            width: '100%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: {
                                        content: 'Ready to Get Started?',
                                        level: 'h2',
                                        align: 'center',
                                        padding: { top: 0, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: 'Join thousands of satisfied customers today.',
                                        variant: 'paragraph',
                                        align: 'center',
                                        padding: { top: 0, right: 0, bottom: 20, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'button',
                                    props: {
                                        text: 'Get Started Now',
                                        href: '#',
                                        variant: 'success',
                                        size: 'lg',
                                        align: 'center',
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        backgroundColor: '#f0f9ff',
                        padding: { top: 40, right: 20, bottom: 40, left: 20 },
                        borderRadius: 8,
                    },
                },
            },
        ],
    },
    {
        id: 'footer-social',
        name: 'Social Footer',
        description: 'Footer with social icons and legal text',
        category: 'section',
        sectionType: 'footer',
        isBuiltIn: true,
        blocks: [
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '1-column',
                    columns: [
                        {
                            id: 'temp',
                            width: '100%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'social',
                                    props: {
                                        links: [
                                            { platform: 'facebook', url: 'https://facebook.com', enabled: true },
                                            { platform: 'twitter', url: 'https://twitter.com', enabled: true },
                                            { platform: 'instagram', url: 'https://instagram.com', enabled: true },
                                            { platform: 'linkedin', url: 'https://linkedin.com', enabled: true },
                                            { platform: 'youtube', url: '', enabled: false },
                                            { platform: 'tiktok', url: '', enabled: false },
                                            { platform: 'pinterest', url: '', enabled: false },
                                            { platform: 'email', url: '', enabled: false },
                                        ],
                                        iconSize: 'md',
                                        iconStyle: 'filled',
                                        align: 'center',
                                        gap: 16,
                                        padding: { top: 0, right: 0, bottom: 20, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'divider',
                                    props: {
                                        style: 'solid',
                                        color: '#e5e7eb',
                                        width: '100%',
                                        thickness: 1,
                                        padding: { top: 0, right: 0, bottom: 20, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: '© 2024 Your Company. All rights reserved.<br><a href="#">Unsubscribe</a> | <a href="#">Privacy Policy</a>',
                                        variant: 'caption',
                                        align: 'center',
                                        fontSize: 12,
                                        color: '#6b7280',
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        backgroundColor: '#f9fafb',
                        padding: { top: 30, right: 20, bottom: 30, left: 20 },
                    },
                },
            },
        ],
    },
    {
        id: 'signature-professional',
        name: 'Email Signature',
        description: 'Professional email signature block',
        category: 'section',
        sectionType: 'signature',
        isBuiltIn: true,
        blocks: [
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '1-column',
                    columns: [
                        {
                            id: 'temp',
                            width: '100%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'html',
                                    props: {
                                        content: `<div style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.4; color: #333333;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="width: auto;">
    <tr>
      <td style="vertical-align: top; padding-right: 16px;">
        <img src="https://placehold.co/80x80/3b82f6/ffffff?text=Photo" alt="Profile Photo" width="80" height="80" style="display: block; border-radius: 50%;">
      </td>
      <td style="vertical-align: top; border-left: 2px solid #3b82f6; padding-left: 16px;">
        <p style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700; color: #111827;">Your Name</p>
        <p style="margin: 0 0 8px 0; font-size: 14px; color: #6b7280;">Job Title | Company Name</p>
        <p style="margin: 0; font-size: 13px; color: #374151;">
          <a href="mailto:email@example.com" style="color: #3b82f6; text-decoration: none;">email@example.com</a><br>
          <a href="https://example.com" style="color: #6b7280; text-decoration: none;">www.example.com</a>
        </p>
      </td>
    </tr>
  </table>
</div>`,
                                        padding: { top: 20, right: 20, bottom: 20, left: 20 },
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        padding: { top: 10, right: 0, bottom: 10, left: 0 },
                    },
                },
            },
        ],
    },
]

/**
 * Full Layout Templates - Complete email designs
 */
export const LAYOUT_TEMPLATES: EmailTemplate[] = [
    {
        id: 'layout-newsletter',
        name: 'Newsletter',
        description: 'Classic newsletter layout with header, content sections, and footer',
        category: 'layout',
        isBuiltIn: true,
        blocks: [
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'header-basic')!.blocks),
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'hero-centered')!.blocks),
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'content-two-column')!.blocks),
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'cta-centered')!.blocks),
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'footer-social')!.blocks),
        ],
    },
    {
        id: 'layout-announcement',
        name: 'Announcement',
        description: 'Simple announcement email with focused message',
        category: 'layout',
        isBuiltIn: true,
        blocks: [
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'header-basic')!.blocks),
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '1-column',
                    columns: [
                        {
                            id: 'temp',
                            width: '100%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: {
                                        content: 'Important Announcement',
                                        level: 'h1',
                                        align: 'center',
                                        padding: { top: 30, right: 0, bottom: 20, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: 'We have exciting news to share with you! Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum euismod, nisi vel consectetur interdum, nisl nunc egestas nunc, vitae tincidunt nisl nunc euismod nunc.',
                                        variant: 'paragraph',
                                        align: 'center',
                                        padding: { top: 0, right: 40, bottom: 30, left: 40 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'button',
                                    props: {
                                        text: 'Learn More',
                                        href: '#',
                                        variant: 'primary',
                                        size: 'lg',
                                        align: 'center',
                                        padding: { top: 0, right: 0, bottom: 30, left: 0 },
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        padding: { top: 20, right: 20, bottom: 20, left: 20 },
                    },
                },
            },
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'footer-social')!.blocks),
        ],
    },
    {
        id: 'layout-welcome',
        name: 'Welcome Email',
        description: 'Warm welcome email for new subscribers',
        category: 'layout',
        isBuiltIn: true,
        blocks: [
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'header-basic')!.blocks),
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '1-column',
                    columns: [
                        {
                            id: 'temp',
                            width: '100%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: {
                                        content: 'Welcome to the Family! 🎉',
                                        level: 'h1',
                                        align: 'center',
                                        padding: { top: 30, right: 0, bottom: 10, left: 0 },
                                    },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: {
                                        content: "We're thrilled to have you join us. Here's what you can expect:",
                                        variant: 'paragraph',
                                        align: 'center',
                                        padding: { top: 0, right: 20, bottom: 30, left: 20 },
                                    },
                                },
                            ],
                        },
                    ],
                    style: {
                        padding: { top: 20, right: 20, bottom: 0, left: 20 },
                    },
                },
            },
            {
                id: 'temp',
                type: 'row',
                props: {
                    preset: '3-equal',
                    gap: 15,
                    stackOnMobile: true,
                    columns: [
                        {
                            id: 'temp',
                            width: '33.33%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: { content: '📧', level: 'h2', align: 'center', padding: { top: 0, right: 0, bottom: 5, left: 0 } },
                                },
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: { content: 'Weekly Updates', level: 'h3', align: 'center', padding: { top: 0, right: 0, bottom: 5, left: 0 } },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: { content: 'Get the latest news every week', variant: 'caption', align: 'center' },
                                },
                            ],
                            style: { backgroundColor: '#f0f9ff', padding: { top: 20, right: 15, bottom: 20, left: 15 }, borderRadius: 8 },
                        },
                        {
                            id: 'temp',
                            width: '33.33%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: { content: '🎁', level: 'h2', align: 'center', padding: { top: 0, right: 0, bottom: 5, left: 0 } },
                                },
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: { content: 'Exclusive Offers', level: 'h3', align: 'center', padding: { top: 0, right: 0, bottom: 5, left: 0 } },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: { content: 'Special deals just for subscribers', variant: 'caption', align: 'center' },
                                },
                            ],
                            style: { backgroundColor: '#fef3c7', padding: { top: 20, right: 15, bottom: 20, left: 15 }, borderRadius: 8 },
                        },
                        {
                            id: 'temp',
                            width: '33.33%',
                            blocks: [
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: { content: '💡', level: 'h2', align: 'center', padding: { top: 0, right: 0, bottom: 5, left: 0 } },
                                },
                                {
                                    id: 'temp',
                                    type: 'heading',
                                    props: { content: 'Tips & Tricks', level: 'h3', align: 'center', padding: { top: 0, right: 0, bottom: 5, left: 0 } },
                                },
                                {
                                    id: 'temp',
                                    type: 'text',
                                    props: { content: 'Expert advice to help you succeed', variant: 'caption', align: 'center' },
                                },
                            ],
                            style: { backgroundColor: '#ecfdf5', padding: { top: 20, right: 15, bottom: 20, left: 15 }, borderRadius: 8 },
                        },
                    ],
                    style: {
                        padding: { top: 0, right: 20, bottom: 30, left: 20 },
                    },
                },
            },
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'cta-centered')!.blocks),
            ...cloneBlocksWithNewIds(SECTION_TEMPLATES.find((t) => t.id === 'footer-social')!.blocks),
        ],
    },
]

/**
 * Get all built-in templates.
 */
export function getBuiltInTemplates(): EmailTemplate[] {
    return [...SECTION_TEMPLATES, ...LAYOUT_TEMPLATES]
}

/**
 * Get templates by category.
 */
export function getTemplatesByCategory(category: 'section' | 'layout'): EmailTemplate[] {
    return category === 'section' ? SECTION_TEMPLATES : LAYOUT_TEMPLATES
}

/**
 * Get a template by ID.
 */
export function getTemplateById(id: string): EmailTemplate | undefined {
    return getBuiltInTemplates().find((t) => t.id === id)
}

/**
 * Get section templates by type.
 */
export function getSectionsByType(sectionType: SectionType): EmailTemplate[] {
    return SECTION_TEMPLATES.filter((t) => t.sectionType === sectionType)
}
