<?php

return [
    'editor' => [
        'sidebar' => [
            'title' => 'Blocks',
            'description' => 'Drag blocks to the canvas',
            'drag_instructions' => 'Drag a block to the canvas, or press Enter to add it at the end.',
            'drag_label' => 'Drag :block block to canvas',
        ],
        'canvas' => [
            'empty_title' => 'No blocks yet',
            'empty_description' => 'Drag blocks from the sidebar to start building your email template',
            'drop_here' => 'Drop block here',
        ],
        'error_boundary' => [
            'title' => 'Something went wrong',
            'description' => 'The editor encountered an error. Your recent changes may not have been saved.',
            'retry' => 'Try again',
        ],
    ],

    'blocks' => [
        'text' => 'Text',
        'image' => 'Image',
        'button' => 'Button',
        'divider' => 'Divider',
        'spacer' => 'Spacer',
        'social' => 'Social',
    ],

    'properties' => [
        'text' => [
            'content' => 'Content',
            'content_placeholder' => 'Enter text content...',
            'variant' => 'Variant',
            'alignment' => 'Alignment',
            'color' => 'Color',
            'font_size' => 'Font Size',
            'line_height' => 'Line Height',
        ],
        'image' => [
            'url' => 'Image URL',
            'url_placeholder' => 'https://example.com/image.jpg',
            'alt' => 'Alt Text',
            'alt_placeholder' => 'Image description...',
            'link_url' => 'Link URL (optional)',
            'link_placeholder' => 'https://example.com',
            'width' => 'Width',
            'width_placeholder' => '100% or 300px',
            'alignment' => 'Alignment',
            'border_radius' => 'Border Radius',
            'fluid_mobile' => 'Fluid on Mobile',
        ],
        'button' => [
            'text' => 'Button Text',
            'text_placeholder' => 'Click Here',
            'link_url' => 'Link URL',
            'link_placeholder' => 'https://example.com',
            'variant' => 'Variant',
            'size' => 'Size',
            'alignment' => 'Alignment',
            'background_color' => 'Background Color',
            'text_color' => 'Text Color',
            'border_radius' => 'Border Radius',
            'full_width' => 'Full Width',
        ],
        'padding' => [
            'title' => 'Padding',
            'top' => 'Top',
            'right' => 'Right',
            'bottom' => 'Bottom',
            'left' => 'Left',
        ],
    ],

    'toolbar' => [
        'move_up' => 'Move block up',
        'move_down' => 'Move block down',
        'duplicate' => 'Duplicate block',
        'delete' => 'Delete block',
        'drag_reorder' => 'Drag to reorder block',
        'actions' => 'Block actions',
    ],

    'variants' => [
        'heading' => 'Heading',
        'subheading' => 'Subheading',
        'paragraph' => 'Paragraph',
        'caption' => 'Caption',
        'primary' => 'Primary',
        'secondary' => 'Secondary',
        'success' => 'Success',
        'danger' => 'Danger',
        'outline' => 'Outline',
    ],

    'sizes' => [
        'small' => 'Small',
        'medium' => 'Medium',
        'large' => 'Large',
    ],

    'alignment' => [
        'left' => 'Left',
        'center' => 'Center',
        'right' => 'Right',
    ],

    'validation' => [
        'invalid_block_structure' => 'Invalid block structure.',
        'block_id_required' => 'Each block must have an id.',
        'block_id_format' => 'Block id must be a valid identifier.',
        'block_type_required' => 'Each block must have a type.',
        'block_type_invalid' => 'Block type ":type" is not registered.',
        'block_props_format' => 'Block props must be an array.',
    ],
];
