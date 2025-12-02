<script setup lang="ts">
import { Input, Select, SelectOption, FormField, RichTextEditor } from '@sabrenski/spire-ui-vue'
import type { TextBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { ALIGNMENT_OPTIONS, COLORS, DEFAULT_PADDING } from '../../constants'
import ColorInput from './ColorInput.vue'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: TextBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<TextBlockProps>]
}>()

const { update, updatePadding, updateInt } = useBlockProperties<TextBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Content" description="Select text to format">
            <RichTextEditor
                :model-value="props.blockProps.content"
                preset="standard"
                :hide-toolbar="true"
                :bubble-menu="true"
                min-height="150px"
                placeholder="Enter text content..."
                @update:model-value="update('content', $event)"
            />
        </FormField>

        <FormField label="Alignment">
            <Select :model-value="props.blockProps.align" @update:model-value="update('align', $event as any)">
                <SelectOption v-for="align in ALIGNMENT_OPTIONS.horizontal" :key="align.value" :value="align.value">
                    {{ align.label }}
                </SelectOption>
            </Select>
        </FormField>

        <ColorInput
            :model-value="props.blockProps.color ?? COLORS.textDefault"
            @update:model-value="update('color', $event)"
        />

        <FormField label="Font Size">
            <Input
                type="number"
                :model-value="String(props.blockProps.fontSize ?? 16)"
                min="10"
                max="72"
                @update:model-value="updateInt('fontSize', $event as string, 16)"
            />
        </FormField>

        <FormField label="Line Height">
            <Input
                type="number"
                :model-value="String(props.blockProps.lineHeight ?? 1.5)"
                min="1"
                max="3"
                step="0.1"
                @update:model-value="update('lineHeight', parseFloat($event as string) || 1.5)"
            />
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
