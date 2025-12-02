<script setup lang="ts">
import { computed } from 'vue'
import type { ColumnData } from '../../types/blocks'
import { useBlockSelection } from '../../composables/useBlockSelection'

interface Props {
    /** The column data */
    column: ColumnData
    /** ID of the parent row block */
    rowId: string
    /** Index of this column within the row */
    index: number
}

const props = defineProps<Props>()

const { isColumnSelected } = useBlockSelection()

const selected = computed(() => isColumnSelected(props.column.id))

const hasBackgroundImage = computed(() => !!props.column.style?.backgroundImage)
const hasOverlay = computed(() => !!props.column.style?.overlayColor)

const backgroundStyles = computed(() => {
    const style = props.column.style
    if (!style?.backgroundImage) return {}
    return {
        backgroundImage: `url(${style.backgroundImage})`,
        backgroundSize: style.backgroundSize ?? 'cover',
        backgroundPosition: style.backgroundPosition ?? 'center center',
        backgroundRepeat: 'no-repeat',
    }
})

const overlayStyles = computed(() => {
    const style = props.column.style
    if (!style?.overlayColor) return {}
    const opacity = (style.overlayOpacity ?? 50) / 100
    return {
        backgroundColor: style.overlayColor,
        opacity: opacity,
        borderRadius: style.borderRadius ? `${style.borderRadius}px` : undefined,
    }
})

const verticalAlignClass = computed(() => {
    const align = props.column.style?.verticalAlign ?? 'top'
    return `email-column--${align}`
})
</script>

<template>
    <div
        v-bind="$attrs"
        class="email-column group/column relative transition-all duration-150"
        :class="[
            verticalAlignClass,
            selected && 'ring-2 ring-primary ring-offset-1',
            column.blocks.length === 0 && 'rounded-lg border border-dashed border-border bg-surface/50',
        ]"
        :style="backgroundStyles"
    >
        <div
            v-if="hasBackgroundImage && hasOverlay"
            class="pointer-events-none absolute inset-0"
            :style="overlayStyles"
        />
        <div class="relative z-[1]">
            <slot />
        </div>
    </div>
</template>
