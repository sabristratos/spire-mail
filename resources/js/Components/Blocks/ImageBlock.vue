<script setup lang="ts">
import { computed } from 'vue'
import { Icon } from '@sabrenski/spire-ui-vue'
import { Image01Icon } from '@hugeicons/core-free-icons'
import type { ImageBlockProps } from '../../types/blocks'

interface Props {
    blockProps: ImageBlockProps
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

const imageStyles = computed(() => {
    const p = props.blockProps
    const align = p.align ?? 'center'

    return {
        width: p.width ?? '100%',
        maxWidth: '100%',
        borderRadius: p.borderRadius ? `${p.borderRadius}px` : '0',
        display: 'block',
        marginLeft: align === 'center' || align === 'right' ? 'auto' : '0',
        marginRight: align === 'center' || align === 'left' ? 'auto' : '0',
    }
})

const hasImage = computed(() => Boolean(props.blockProps.src))
</script>

<template>
    <div
        class="email-image-wrapper"
        :class="`email-image-wrapper--${props.blockProps.align ?? 'center'}`"
        :style="containerStyles"
    >
        <template v-if="hasImage">
            <a v-if="props.blockProps.href" :href="props.blockProps.href" target="_blank" rel="noopener">
                <img class="email-image" :src="props.blockProps.src" :alt="props.blockProps.alt || ''" :style="imageStyles" />
            </a>
            <img v-else class="email-image" :src="props.blockProps.src" :alt="props.blockProps.alt || ''" :style="imageStyles" />
        </template>

        <div
            v-else
            class="flex flex-col items-center justify-center gap-2 rounded-lg border-2 border-dashed border-border bg-subtle p-8"
        >
            <Icon :name="Image01Icon" size="lg" class="text-foreground-muted" />
            <span class="text-sm text-foreground-muted">No image selected</span>
        </div>
    </div>
</template>
