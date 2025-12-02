<script setup lang="ts">
import { Alert, FormField, Textarea } from '@sabrenski/spire-ui-vue'
import type { HtmlBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { DEFAULT_PADDING } from '../../constants'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: HtmlBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<HtmlBlockProps>]
}>()

const { update, updatePadding } = useBlockProperties<HtmlBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <Alert color="warning" hide-icon-wrapper>
            Raw HTML will be rendered without sanitization. Use with caution.
        </Alert>

        <FormField label="HTML Content">
            <Textarea
                :model-value="props.blockProps.content"
                placeholder="<div>Your HTML here...</div>"
                :rows="8"
                class="font-mono text-sm"
                @update:model-value="update('content', $event)"
            />
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
