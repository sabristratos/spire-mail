<script setup lang="ts">
/**
 * Default layout wrapper for Spire Mail pages.
 * Users can replace this with their own layout after publishing pages.
 */
interface Props {
    /** Optional page title */
    title?: string
}

defineProps<Props>()
</script>

<template>
    <div class="min-h-screen bg-canvas">
        <slot />
    </div>
</template>
