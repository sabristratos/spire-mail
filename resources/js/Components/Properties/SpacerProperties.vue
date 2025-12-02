<script setup lang="ts">
import { FormField, Slider } from '@sabrenski/spire-ui-vue'
import type { SpacerBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import ColorInput from './ColorInput.vue'

interface Props {
    blockProps: SpacerBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<SpacerBlockProps>]
}>()

const { update } = useBlockProperties<SpacerBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Height">
            <Slider
                :model-value="props.blockProps.height ?? 40"
                :min="10"
                :max="200"
                :step="5"
                @update:model-value="update('height', $event)"
            />
            <span class="text-xs text-foreground-muted">{{ props.blockProps.height ?? 40 }}px</span>
        </FormField>

        <ColorInput
            label="Background Color"
            :model-value="props.blockProps.backgroundColor ?? 'transparent'"
            @update:model-value="update('backgroundColor', $event)"
        />
    </div>
</template>
