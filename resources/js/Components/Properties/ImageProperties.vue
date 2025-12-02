<script setup lang="ts">
import { Input, Select, SelectOption, FormField, Switch } from '@sabrenski/spire-ui-vue'
import type { ImageBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { ALIGNMENT_OPTIONS, DEFAULT_PADDING } from '../../constants'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: ImageBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<ImageBlockProps>]
}>()

const { update, updatePadding, updateInt } = useBlockProperties<ImageBlockProps>(emit)
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Image URL">
            <Input
                :model-value="props.blockProps.src"
                placeholder="https://example.com/image.jpg"
                @update:model-value="update('src', $event as string)"
            />
        </FormField>

        <FormField label="Alt Text">
            <Input
                :model-value="props.blockProps.alt"
                placeholder="Image description..."
                @update:model-value="update('alt', $event as string)"
            />
        </FormField>

        <FormField label="Link URL (optional)">
            <Input
                :model-value="props.blockProps.href ?? ''"
                placeholder="https://example.com"
                @update:model-value="update('href', $event as string)"
            />
        </FormField>

        <FormField label="Width">
            <Input
                :model-value="props.blockProps.width ?? '100%'"
                placeholder="100% or 300px"
                @update:model-value="update('width', $event as string)"
            />
        </FormField>

        <FormField label="Alignment">
            <Select :model-value="props.blockProps.align" @update:model-value="update('align', $event as any)">
                <SelectOption v-for="align in ALIGNMENT_OPTIONS.horizontal" :key="align.value" :value="align.value">
                    {{ align.label }}
                </SelectOption>
            </Select>
        </FormField>

        <FormField label="Border Radius">
            <Input
                type="number"
                :model-value="String(props.blockProps.borderRadius ?? 0)"
                min="0"
                max="50"
                @update:model-value="updateInt('borderRadius', $event as string, 0)"
            />
        </FormField>

        <FormField label="Fluid on Mobile">
            <Switch
                :model-value="props.blockProps.fluidOnMobile ?? true"
                @update:model-value="update('fluidOnMobile', $event)"
            />
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
