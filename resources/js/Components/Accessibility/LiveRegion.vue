<script setup lang="ts">
import { useAnnouncer } from '../../composables/useAnnouncer'

const { announcement, priority } = useAnnouncer()
</script>

<template>
    <div
        :aria-live="priority"
        aria-atomic="true"
        class="sr-only"
    >
        {{ announcement }}
    </div>
</template>
