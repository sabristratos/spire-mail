<script setup lang="ts">
import { Input, Select, SelectOption, FormField, Textarea } from '@sabrenski/spire-ui-vue'
import type { HeadingBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { HEADING_LEVELS, ALIGNMENT_OPTIONS, FONT_WEIGHTS, COLORS, DEFAULT_PADDING } from '../../constants'
import ColorInput from './ColorInput.vue'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: HeadingBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<HeadingBlockProps>]
}>()

const { update, updatePadding, updateInt } = useBlockProperties<HeadingBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Content">
            <Textarea
                :model-value="props.blockProps.content"
                placeholder="Enter heading text..."
                rows="2"
                @update:model-value="update('content', $event)"
            />
        </FormField>

        <FormField label="Level">
            <Select :model-value="props.blockProps.level" @update:model-value="update('level', $event as any)">
                <SelectOption v-for="level in HEADING_LEVELS" :key="level.value" :value="level.value">
                    {{ level.label }}
                </SelectOption>
            </Select>
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
                :model-value="String(props.blockProps.fontSize ?? 28)"
                min="12"
                max="72"
                @update:model-value="updateInt('fontSize', $event as string, 28)"
            />
        </FormField>

        <FormField label="Font Weight">
            <Select :model-value="props.blockProps.fontWeight ?? 'bold'" @update:model-value="update('fontWeight', $event as any)">
                <SelectOption v-for="weight in FONT_WEIGHTS" :key="weight.value" :value="weight.value">
                    {{ weight.label }}
                </SelectOption>
            </Select>
        </FormField>

        <FormField label="Line Height">
            <Input
                type="number"
                :model-value="String(props.blockProps.lineHeight ?? 1.2)"
                min="1"
                max="3"
                step="0.1"
                @update:model-value="update('lineHeight', parseFloat($event as string) || 1.2)"
            />
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
