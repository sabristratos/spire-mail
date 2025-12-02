<script setup lang="ts">
import { computed } from 'vue'
import type { TextBlockProps } from '../../types/blocks'
import { useSanitize } from '../../composables/useSanitize'

interface Props {
    blockProps: TextBlockProps
}

const props = defineProps<Props>()
const { sanitize } = useSanitize()

const sanitizedContent = computed(() => sanitize(props.blockProps.content ?? ''))

const styles = computed(() => {
    const p = props.blockProps
    const padding = p.padding ?? { top: 10, right: 0, bottom: 10, left: 0 }

    return {
        textAlign: p.align ?? 'left',
        color: p.color ?? '#333333',
        fontSize: `${p.fontSize ?? 16}px`,
        fontWeight: getFontWeight(p.fontWeight),
        lineHeight: p.lineHeight ?? 1.5,
        padding: `${padding.top ?? 0}px ${padding.right ?? 0}px ${padding.bottom ?? 0}px ${padding.left ?? 0}px`,
    }
})

function getFontWeight(fontWeight?: string): string {
    switch (fontWeight) {
        case 'medium':
            return '500'
        case 'semibold':
            return '600'
        case 'bold':
            return '700'
        default:
            return 'normal'
    }
}
</script>

<template>
    <div class="email-text" :style="styles" v-html="sanitizedContent" />
</template>
