<script setup lang="ts">
import { computed } from 'vue'
import type { ButtonBlockProps } from '../../types/blocks'

interface Props {
    blockProps: ButtonBlockProps
}

const props = defineProps<Props>()

const containerStyles = computed(() => {
    const p = props.blockProps
    const padding = p.padding ?? { top: 10, right: 0, bottom: 10, left: 0 }

    return {
        textAlign: p.align ?? 'center',
        padding: `${padding.top ?? 0}px ${padding.right ?? 0}px ${padding.bottom ?? 0}px ${padding.left ?? 0}px`,
    }
})

const buttonStyles = computed(() => {
    const p = props.blockProps

    const backgroundColor = p.backgroundColor ?? getDefaultBackgroundColor(p.variant)
    const textColor = p.textColor ?? getDefaultTextColor(p.variant)
    const innerPadding = getInnerPadding(p.size ?? 'md')

    return {
        display: 'inline-block',
        backgroundColor,
        color: textColor,
        padding: innerPadding,
        borderRadius: `${p.borderRadius ?? 6}px`,
        fontWeight: '600',
        fontSize: getFontSize(p.size ?? 'md'),
        textDecoration: 'none',
        width: p.fullWidth ? '100%' : 'auto',
        border: p.variant === 'outline' ? `2px solid ${textColor}` : 'none',
    }
})

function getDefaultBackgroundColor(variant?: string): string {
    switch (variant) {
        case 'primary':
            return '#3b82f6'
        case 'secondary':
            return '#6b7280'
        case 'success':
            return '#10b981'
        case 'danger':
            return '#ef4444'
        case 'outline':
            return 'transparent'
        default:
            return '#3b82f6'
    }
}

function getDefaultTextColor(variant?: string): string {
    return variant === 'outline' ? '#3b82f6' : '#ffffff'
}

function getInnerPadding(size: string): string {
    switch (size) {
        case 'sm':
            return '8px 16px'
        case 'lg':
            return '16px 32px'
        default:
            return '12px 24px'
    }
}

function getFontSize(size: string): string {
    switch (size) {
        case 'sm':
            return '14px'
        case 'lg':
            return '18px'
        default:
            return '16px'
    }
}
</script>

<template>
    <div
        class="email-button-wrapper"
        :class="`email-button-wrapper--${props.blockProps.align ?? 'center'}`"
        :style="containerStyles"
    >
        <a class="email-button" :href="props.blockProps.href || '#'" :style="buttonStyles" @click.prevent>
            {{ props.blockProps.text || 'Click Here' }}
        </a>
    </div>
</template>
