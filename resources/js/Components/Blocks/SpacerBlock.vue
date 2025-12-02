<script setup lang="ts">
import { computed } from 'vue'
import type { SpacerBlockProps } from '../../types/blocks'

interface Props {
    blockProps: SpacerBlockProps
}

const props = defineProps<Props>()

const styles = computed(() => ({
    height: `${props.blockProps.height ?? 40}px`,
    backgroundColor: props.blockProps.backgroundColor ?? 'transparent',
}))
</script>

<template>
    <div class="email-spacer" :style="styles" />
</template>
