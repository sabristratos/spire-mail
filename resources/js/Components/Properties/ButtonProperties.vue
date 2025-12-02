<script setup lang="ts">
import { Input, Select, SelectOption, FormField, Switch } from '@sabrenski/spire-ui-vue'
import type { ButtonBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { BUTTON_VARIANTS, BUTTON_SIZES, ALIGNMENT_OPTIONS, COLORS, DEFAULT_PADDING } from '../../constants'
import ColorInput from './ColorInput.vue'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: ButtonBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<ButtonBlockProps>]
}>()

const { update, updatePadding, updateInt } = useBlockProperties<ButtonBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Button Text">
            <Input
                :model-value="props.blockProps.text"
                placeholder="Click Here"
                @update:model-value="update('text', $event as string)"
            />
        </FormField>

        <FormField label="Link URL">
            <Input
                :model-value="props.blockProps.href"
                placeholder="https://example.com"
                @update:model-value="update('href', $event as string)"
            />
        </FormField>

        <FormField label="Variant">
            <Select :model-value="props.blockProps.variant" @update:model-value="update('variant', $event as any)">
                <SelectOption v-for="variant in BUTTON_VARIANTS" :key="variant.value" :value="variant.value">
                    {{ variant.label }}
                </SelectOption>
            </Select>
        </FormField>

        <FormField label="Size">
            <Select :model-value="props.blockProps.size" @update:model-value="update('size', $event as any)">
                <SelectOption v-for="size in BUTTON_SIZES" :key="size.value" :value="size.value">
                    {{ size.label }}
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
            label="Background Color"
            :model-value="props.blockProps.backgroundColor ?? COLORS.primary"
            @update:model-value="update('backgroundColor', $event)"
        />

        <ColorInput
            label="Text Color"
            :model-value="props.blockProps.textColor ?? COLORS.textOnPrimary"
            @update:model-value="update('textColor', $event)"
        />

        <FormField label="Border Radius">
            <Input
                type="number"
                :model-value="String(props.blockProps.borderRadius ?? 6)"
                min="0"
                max="50"
                @update:model-value="updateInt('borderRadius', $event as string, 6)"
            />
        </FormField>

        <FormField label="Full Width">
            <Switch
                :model-value="props.blockProps.fullWidth ?? false"
                @update:model-value="update('fullWidth', $event)"
            />
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
