<script setup lang="ts">
import { ColorPicker, FormField } from '@sabrenski/spire-ui-vue'

/**
 * Props for the ColorInput component.
 */
export interface ColorInputProps {
    /** Current hex color value */
    modelValue: string
    /** Label for the form field */
    label?: string
    /** Whether the input is disabled */
    disabled?: boolean
}

const props = withDefaults(defineProps<ColorInputProps>(), {
    label: 'Color',
    disabled: false,
})

const emit = defineEmits<{
    'update:modelValue': [value: string]
}>()

function handleColorChange(value: string): void {
    emit('update:modelValue', value)
}
</script>

<template>
    <FormField :label="props.label">
        <ColorPicker
            :model-value="props.modelValue"
            :disabled="props.disabled"
            format="hex"
            size="sm"
            class="w-full"
            @update:model-value="handleColorChange"
        />
    </FormField>
</template>
