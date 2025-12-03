<script setup lang="ts">
import { computed } from 'vue'
import { Input, Select, SelectOption, FormField, Switch, ColorPicker, Tabs, TabList, Tab, TabPanel } from '@sabrenski/spire-ui-vue'
import type { RowBlockProps, ColumnPreset, RowStyle, ColumnStyle } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { COLUMN_PRESETS, DEFAULT_ROW_PADDING } from '../../constants'
import PaddingControl from './PaddingControl.vue'
import { updateColumnsPreset, updateColumnWidths, updateRowStyle, updateColumnStyle } from '../../stores/editorStore'

interface Props {
    blockProps: RowBlockProps
    blockId: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<RowBlockProps>]
}>()

const { update, updateInt } = useBlockProperties<RowBlockProps>(emit)

const isCustomPreset = computed(() => props.blockProps.preset === 'custom')

const columnWidths = computed(() => {
    return props.blockProps.columns.map((col) => col.width.replace('%', ''))
})

const rowStyle = computed(() => props.blockProps.style ?? {})

function handlePresetChange(preset: ColumnPreset): void {
    updateColumnsPreset(props.blockId, preset)
}

function handleWidthChange(index: number, value: string): void {
    const numValue = parseInt(value) || 0
    const widths = props.blockProps.columns.map((col, i) => {
        if (i === index) {
            return `${numValue}%`
        }
        return col.width
    })
    updateColumnWidths(props.blockId, widths)
}

function handleRowStyleUpdate(key: keyof RowStyle, value: unknown): void {
    updateRowStyle(props.blockId, { [key]: value })
}

function handleRowPaddingUpdate(padding: { top?: number; right?: number; bottom?: number; left?: number }): void {
    updateRowStyle(props.blockId, { padding })
}

function handleColumnStyleUpdate(columnIndex: number, key: keyof ColumnStyle, value: unknown): void {
    updateColumnStyle(props.blockId, columnIndex, { [key]: value })
}

function handleColumnPaddingUpdate(columnIndex: number, padding: { top?: number; right?: number; bottom?: number; left?: number }): void {
    updateColumnStyle(props.blockId, columnIndex, { padding })
}
</script>

<template>
    <div class="space-y-4 p-4">
        <Tabs size="sm" :full-width="true" default-value="layout">
            <TabList >
                <Tab tab-key="layout">Layout</Tab>
                <Tab tab-key="row-style">Row Style</Tab>
                <Tab tab-key="column-style">Column Style</Tab>
            </TabList>

            <TabPanel tab-key="layout">
                <div class="space-y-4 pt-4">
                    <FormField label="Layout Preset">
                        <Select
                            :model-value="blockProps.preset"
                            @update:model-value="handlePresetChange($event as ColumnPreset)"
                        >
                            <SelectOption
                                v-for="preset in COLUMN_PRESETS"
                                :key="preset.value"
                                :value="preset.value"
                            >
                                {{ preset.label }}
                            </SelectOption>
                        </Select>
                    </FormField>

                    <template v-if="isCustomPreset">
                        <FormField label="Column Widths (%)">
                            <div class="flex gap-2">
                                <Input
                                    v-for="(width, index) in columnWidths"
                                    :key="index"
                                    type="number"
                                    :model-value="width"
                                    min="10"
                                    max="90"
                                    class="w-16"
                                    @update:model-value="handleWidthChange(index, $event as string)"
                                />
                            </div>
                            <p class="mt-1 text-xs text-foreground-muted">
                                Total: {{ columnWidths.reduce((sum, w) => sum + (parseInt(w) || 0), 0) }}%
                            </p>
                        </FormField>
                    </template>

                    <FormField label="Column Gap (px)">
                        <Input
                            type="number"
                            :model-value="String(blockProps.gap ?? 10)"
                            min="0"
                            max="60"
                            @update:model-value="updateInt('gap', $event as string, 0)"
                        />
                    </FormField>

                    <FormField label="Stack on Mobile">
                        <Switch
                            :model-value="blockProps.stackOnMobile ?? true"
                            @update:model-value="update('stackOnMobile', $event)"
                        />
                        <p class="mt-1 text-xs text-foreground-muted">
                            Columns will stack vertically on small screens
                        </p>
                    </FormField>
                </div>
            </TabPanel>

            <TabPanel tab-key="row-style">
                <div class="space-y-4 pt-4">
                    <FormField label="Background Color">
                        <ColorPicker
                            :model-value="rowStyle.backgroundColor ?? ''"
                            @update:model-value="handleRowStyleUpdate('backgroundColor', $event)"
                        />
                    </FormField>

                    <FormField label="Background Image URL">
                        <Input
                            :model-value="rowStyle.backgroundImage ?? ''"
                            placeholder="https://..."
                            @update:model-value="handleRowStyleUpdate('backgroundImage', $event)"
                        />
                    </FormField>

                    <template v-if="rowStyle.backgroundImage">
                        <FormField label="Background Size">
                            <Select
                                :model-value="rowStyle.backgroundSize ?? 'cover'"
                                @update:model-value="handleRowStyleUpdate('backgroundSize', $event)"
                            >
                                <SelectOption value="cover">Cover</SelectOption>
                                <SelectOption value="contain">Contain</SelectOption>
                                <SelectOption value="auto">Auto</SelectOption>
                            </Select>
                        </FormField>

                        <FormField label="Background Position">
                            <Input
                                :model-value="rowStyle.backgroundPosition ?? 'center center'"
                                placeholder="center center"
                                @update:model-value="handleRowStyleUpdate('backgroundPosition', $event)"
                            />
                        </FormField>
                    </template>

                    <FormField label="Text Align">
                        <Select
                            :model-value="rowStyle.textAlign ?? 'left'"
                            @update:model-value="handleRowStyleUpdate('textAlign', $event)"
                        >
                            <SelectOption value="left">Left</SelectOption>
                            <SelectOption value="center">Center</SelectOption>
                            <SelectOption value="right">Right</SelectOption>
                        </Select>
                    </FormField>

                    <FormField label="Border Radius (px)">
                        <Input
                            type="number"
                            :model-value="String(rowStyle.borderRadius ?? 0)"
                            min="0"
                            max="50"
                            @update:model-value="handleRowStyleUpdate('borderRadius', parseInt($event as string) || 0)"
                        />
                    </FormField>

                    <PaddingControl
                        label="Row Padding"
                        :model-value="rowStyle.padding ?? DEFAULT_ROW_PADDING"
                        @update:model-value="handleRowPaddingUpdate"
                    />
                </div>
            </TabPanel>

            <TabPanel tab-key="column-style">
                <div class="space-y-6 pt-4">
                    <div
                        v-for="(column, index) in blockProps.columns"
                        :key="column.id"
                        class="space-y-4 rounded-lg border border-border p-3"
                    >
                        <h4 class="text-sm font-medium text-foreground">
                            Column {{ index + 1 }} ({{ column.width }})
                        </h4>

                        <FormField label="Background Color">
                            <ColorPicker
                                :model-value="column.style?.backgroundColor ?? ''"
                                @update:model-value="handleColumnStyleUpdate(index, 'backgroundColor', $event)"
                            />
                        </FormField>

                        <FormField label="Vertical Align">
                            <Select
                                :model-value="column.style?.verticalAlign ?? 'top'"
                                @update:model-value="handleColumnStyleUpdate(index, 'verticalAlign', $event)"
                            >
                                <SelectOption value="top">Top</SelectOption>
                                <SelectOption value="middle">Middle</SelectOption>
                                <SelectOption value="bottom">Bottom</SelectOption>
                            </Select>
                        </FormField>

                        <FormField label="Border Radius (px)">
                            <Input
                                type="number"
                                :model-value="String(column.style?.borderRadius ?? 0)"
                                min="0"
                                max="50"
                                @update:model-value="handleColumnStyleUpdate(index, 'borderRadius', parseInt($event as string) || 0)"
                            />
                        </FormField>

                        <PaddingControl
                            label="Column Padding"
                            :model-value="column.style?.padding ?? { top: 0, right: 0, bottom: 0, left: 0 }"
                            @update:model-value="handleColumnPaddingUpdate(index, $event)"
                        />
                    </div>
                </div>
            </TabPanel>
        </Tabs>
    </div>
</template>
