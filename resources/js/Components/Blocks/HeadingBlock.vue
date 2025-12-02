<script setup lang="ts">
import { computed } from 'vue'
import type { HeadingBlockProps } from '../../types/blocks'

interface Props {
    blockProps: HeadingBlockProps
}

const props = defineProps<Props>()

const styles = computed(() => {
    const p = props.blockProps
    const padding = p.padding ?? { top: 10, right: 0, bottom: 10, left: 0 }

    return {
        textAlign: p.align ?? 'left',
        color: p.color ?? '#333333',
        fontSize: `${p.fontSize ?? 28}px`,
        fontWeight: getFontWeight(p.fontWeight),
        lineHeight: p.lineHeight ?? 1.2,
        padding: `${padding.top ?? 0}px ${padding.right ?? 0}px ${padding.bottom ?? 0}px ${padding.left ?? 0}px`,
        margin: 0,
    }
})

function getFontWeight(fontWeight?: string): string {
    switch (fontWeight) {
        case 'normal':
            return 'normal'
        case 'medium':
            return '500'
        case 'semibold':
            return '600'
        case 'bold':
        default:
            return '700'
    }
}

const displayContent = computed(() => props.blockProps.content || 'Heading')
</script>

<template>
    <component :is="blockProps.level ?? 'h1'" class="email-heading" :style="styles">
        {{ displayContent }}
    </component>
</template>
