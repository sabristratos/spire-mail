<script setup lang="ts">
import { Input, FormField } from '@sabrenski/spire-ui-vue'
import type { SpacingValue } from '../../types/blocks'

/**
 * Props for the PaddingControl component.
 */
export interface PaddingControlProps {
    /** Current padding values for all sides */
    modelValue: SpacingValue
    /** Label for the section header */
    label?: string
    /** Default values for each side */
    defaults?: SpacingValue
}

const props = withDefaults(defineProps<PaddingControlProps>(), {
    label: 'Padding',
    defaults: () => ({ top: 10, right: 0, bottom: 10, left: 0 }),
})

const emit = defineEmits<{
    'update:modelValue': [value: SpacingValue]
}>()

function updateSide(side: keyof SpacingValue, value: string): void {
    const newPadding = { ...props.modelValue }
    newPadding[side] = parseInt(value) || 0
    emit('update:modelValue', newPadding)
}

function getValue(side: keyof SpacingValue): string {
    return String(props.modelValue?.[side] ?? props.defaults[side] ?? 0)
}
</script>

<template>
    <div class="border-t border-border pt-4">
        <h3 class="mb-2 text-sm font-medium text-foreground">{{ props.label }}</h3>
        <div class="grid grid-cols-2 gap-2">
            <FormField label="Top" size="sm">
                <Input
                    type="number"
                    :model-value="getValue('top')"
                    min="0"
                    @update:model-value="updateSide('top', $event as string)"
                />
            </FormField>
            <FormField label="Right" size="sm">
                <Input
                    type="number"
                    :model-value="getValue('right')"
                    min="0"
                    @update:model-value="updateSide('right', $event as string)"
                />
            </FormField>
            <FormField label="Bottom" size="sm">
                <Input
                    type="number"
                    :model-value="getValue('bottom')"
                    min="0"
                    @update:model-value="updateSide('bottom', $event as string)"
                />
            </FormField>
            <FormField label="Left" size="sm">
                <Input
                    type="number"
                    :model-value="getValue('left')"
                    min="0"
                    @update:model-value="updateSide('left', $event as string)"
                />
            </FormField>
        </div>
    </div>
</template>
