<script setup lang="ts">
import { computed } from 'vue'
import { Icon } from '@sabrenski/spire-ui-vue'
import { SourceCodeIcon } from '@hugeicons/core-free-icons'
import type { HtmlBlockProps } from '../../types/blocks'

interface Props {
    blockProps: HtmlBlockProps
}

const props = defineProps<Props>()

const padding = computed(() => {
    const p = props.blockProps.padding ?? { top: 10, right: 0, bottom: 10, left: 0 }
    return `${p.top ?? 0}px ${p.right ?? 0}px ${p.bottom ?? 0}px ${p.left ?? 0}px`
})

const hasContent = computed(() => !!props.blockProps.content?.trim())

const srcdocContent = computed(() => {
    if (!hasContent.value) return ''

    return `<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            margin: 0;
            padding: 8px;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>
<body>${props.blockProps.content}</body>
</html>`
})
</script>

<template>
    <div class="html-block" :style="{ padding }">
        <div v-if="!hasContent" class="placeholder">
            <Icon :name="SourceCodeIcon" size="lg" class="text-foreground-muted" />
            <span class="text-sm text-foreground-muted">HTML Block</span>
        </div>
        <iframe
            v-else
            :srcdoc="srcdocContent"
            sandbox="allow-same-origin"
            class="html-iframe"
            title="HTML Preview"
        />
    </div>
</template>

<style scoped>
.html-block {
    width: 100%;
    min-height: 60px;
}

.placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 24px;
    border: 1px dashed var(--color-border);
    border-radius: 4px;
    background: var(--color-surface);
}

.html-iframe {
    width: 100%;
    min-height: 80px;
    border: 1px solid var(--color-border);
    border-radius: 4px;
    background: white;
}
</style>
