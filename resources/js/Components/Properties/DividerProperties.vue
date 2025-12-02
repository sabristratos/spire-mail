<script setup lang="ts">
import { Input, Select, SelectOption, FormField } from '@sabrenski/spire-ui-vue'
import type { DividerBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { DIVIDER_STYLES, DIVIDER_WIDTHS, ALIGNMENT_OPTIONS, COLORS, DEFAULT_DIVIDER_PADDING } from '../../constants'
import ColorInput from './ColorInput.vue'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: DividerBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<DividerBlockProps>]
}>()

const { update, updatePadding, updateInt } = useBlockProperties<DividerBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Style">
            <Select :model-value="props.blockProps.style" @update:model-value="update('style', $event as DividerBlockProps['style'])">
                <SelectOption v-for="style in DIVIDER_STYLES" :key="style.value" :value="style.value">
                    {{ style.label }}
                </SelectOption>
            </Select>
        </FormField>

        <template v-if="props.blockProps.style !== 'none'">
            <ColorInput
                label="Line Color"
                :model-value="props.blockProps.color ?? COLORS.divider"
                @update:model-value="update('color', $event)"
            />

            <FormField label="Line Width">
                <Select :model-value="props.blockProps.width" @update:model-value="update('width', $event as string)">
                    <SelectOption v-for="width in DIVIDER_WIDTHS" :key="width.value" :value="width.value">
                        {{ width.label }}
                    </SelectOption>
                </Select>
            </FormField>

            <FormField label="Alignment">
                <Select :model-value="props.blockProps.align" @update:model-value="update('align', $event as DividerBlockProps['align'])">
                    <SelectOption v-for="align in ALIGNMENT_OPTIONS.horizontal" :key="align.value" :value="align.value">
                        {{ align.label }}
                    </SelectOption>
                </Select>
            </FormField>

            <FormField label="Thickness (px)">
                <Input
                    type="number"
                    :model-value="String(props.blockProps.thickness ?? 1)"
                    min="1"
                    max="10"
                    @update:model-value="updateInt('thickness', $event as string, 1)"
                />
            </FormField>
        </template>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_DIVIDER_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
