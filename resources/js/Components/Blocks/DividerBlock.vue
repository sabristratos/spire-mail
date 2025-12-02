<script setup lang="ts">
import { computed } from 'vue'
import type { DividerBlockProps } from '../../types/blocks'

interface Props {
    blockProps: DividerBlockProps
}

const props = defineProps<Props>()

const containerStyles = computed(() => {
    const p = props.blockProps
    const padding = p.padding ?? { top: 20, right: 0, bottom: 20, left: 0 }

    return {
        padding: `${padding.top ?? 0}px ${padding.right ?? 0}px ${padding.bottom ?? 0}px ${padding.left ?? 0}px`,
    }
})

const isSpacerOnly = computed(() => props.blockProps.style === 'none')

const dividerStyles = computed(() => {
    const p = props.blockProps
    const styles: Record<string, string> = {
        borderTopColor: p.color ?? '#e5e7eb',
        borderTopWidth: `${p.thickness ?? 1}px`,
    }

    if (p.width && p.width !== '100%') {
        styles.width = p.width
        styles.margin = p.align === 'center' ? '0 auto' : p.align === 'right' ? '0 0 0 auto' : '0'
    }

    return styles
})

const styleClass = computed(() => {
    const style = props.blockProps.style ?? 'solid'
    if (style === 'none') return ''
    return `email-divider--${style}`
})
</script>

<template>
    <div :style="containerStyles">
        <div
            v-if="!isSpacerOnly"
            class="email-divider"
            :class="styleClass"
            :style="dividerStyles"
        />
    </div>
</template>
