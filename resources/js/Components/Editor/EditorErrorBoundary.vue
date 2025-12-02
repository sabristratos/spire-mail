<script setup lang="ts">
import { ref, onErrorCaptured } from 'vue'
import { Button, Icon } from '@sabrenski/spire-ui-vue'
import { Alert02Icon, RefreshIcon } from '@hugeicons/core-free-icons'

const hasError = ref(false)
const errorMessage = ref('')

const emit = defineEmits<{
    error: [error: Error]
}>()

onErrorCaptured((error: Error) => {
    hasError.value = true
    errorMessage.value = error.message
    emit('error', error)
    console.error('[EditorErrorBoundary] Caught error:', error)
    return false
})

function handleRetry(): void {
    hasError.value = false
    errorMessage.value = ''
}
</script>

<template>
    <div v-if="hasError" class="flex h-full flex-col items-center justify-center gap-4 p-8 text-center">
        <div class="rounded-full bg-danger-subtle p-4">
            <Icon :name="Alert02Icon" size="xl" class="text-danger" />
        </div>
        <div class="space-y-1">
            <h2 class="text-lg font-semibold text-foreground">Something went wrong</h2>
            <p class="max-w-sm text-sm text-foreground-muted">
                The editor encountered an error. Your recent changes may not have been saved.
            </p>
        </div>
        <Button @click="handleRetry">
            <Icon :name="RefreshIcon" size="sm" />
            <span>Try again</span>
        </Button>
    </div>
    <slot v-else />
</template>
