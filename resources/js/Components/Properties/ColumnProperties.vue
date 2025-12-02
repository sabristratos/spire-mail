<script setup lang="ts">
import { computed } from 'vue'
import { Input, Select, SelectOption, FormField, ColorPicker, Tabs, TabList, Tab, TabPanel, Slider } from '@sabrenski/spire-ui-vue'
import type { ColumnData, ColumnStyle } from '../../types/blocks'
import PaddingControl from './PaddingControl.vue'
import { updateColumnStyle } from '../../stores/editorStore'

interface Props {
    /** The column data */
    column: ColumnData
    /** ID of the parent row block */
    rowId: string
    /** Index of this column within the row */
    columnIndex: number
}

const props = defineProps<Props>()

const columnStyle = computed(() => props.column.style ?? {})

function handleStyleUpdate(key: keyof ColumnStyle, value: unknown): void {
    updateColumnStyle(props.rowId, props.columnIndex, { [key]: value })
}

function handlePaddingUpdate(padding: { top?: number; right?: number; bottom?: number; left?: number }): void {
    updateColumnStyle(props.rowId, props.columnIndex, { padding })
}
</script>

<template>
    <div class="space-y-4 p-4">
        <div class="border-b border-border pb-3">
            <h3 class="text-sm font-medium text-foreground">
                Column {{ columnIndex + 1 }}
            </h3>
            <p class="text-xs text-foreground-muted">
                Width: {{ column.width }}
            </p>
        </div>

        <Tabs size="sm" :full-width="true" default-value="style">
            <TabList>
                <Tab tab-key="style">Style</Tab>
                <Tab tab-key="background">Background</Tab>
            </TabList>

            <TabPanel tab-key="style">
                <div class="space-y-4 pt-4">
                    <FormField label="Background Color">
                        <ColorPicker
                            :model-value="columnStyle.backgroundColor ?? ''"
                            @update:model-value="handleStyleUpdate('backgroundColor', $event)"
                        />
                    </FormField>

                    <FormField label="Vertical Align">
                        <Select
                            :model-value="columnStyle.verticalAlign ?? 'top'"
                            @update:model-value="handleStyleUpdate('verticalAlign', $event)"
                        >
                            <SelectOption value="top">Top</SelectOption>
                            <SelectOption value="middle">Middle</SelectOption>
                            <SelectOption value="bottom">Bottom</SelectOption>
                        </Select>
                    </FormField>

                    <FormField label="Border Radius (px)">
                        <Input
                            type="number"
                            :model-value="String(columnStyle.borderRadius ?? 0)"
                            min="0"
                            max="50"
                            @update:model-value="handleStyleUpdate('borderRadius', parseInt($event as string) || 0)"
                        />
                    </FormField>

                    <PaddingControl
                        label="Column Padding"
                        :model-value="columnStyle.padding ?? { top: 0, right: 0, bottom: 0, left: 0 }"
                        @update:model-value="handlePaddingUpdate"
                    />
                </div>
            </TabPanel>

            <TabPanel tab-key="background">
                <div class="space-y-4 pt-4">
                    <FormField label="Background Image URL">
                        <Input
                            :model-value="columnStyle.backgroundImage ?? ''"
                            placeholder="https://..."
                            @update:model-value="handleStyleUpdate('backgroundImage', $event)"
                        />
                    </FormField>

                    <template v-if="columnStyle.backgroundImage">
                        <FormField label="Background Size">
                            <Select
                                :model-value="columnStyle.backgroundSize ?? 'cover'"
                                @update:model-value="handleStyleUpdate('backgroundSize', $event)"
                            >
                                <SelectOption value="cover">Cover</SelectOption>
                                <SelectOption value="contain">Contain</SelectOption>
                                <SelectOption value="auto">Auto</SelectOption>
                            </Select>
                        </FormField>

                        <FormField label="Background Position">
                            <Input
                                :model-value="columnStyle.backgroundPosition ?? 'center center'"
                                placeholder="center center"
                                @update:model-value="handleStyleUpdate('backgroundPosition', $event)"
                            />
                        </FormField>
                    </template>

                    <div class="border-t border-border pt-4">
                        <h4 class="mb-3 text-xs font-medium uppercase tracking-wide text-foreground-muted">
                            Overlay
                        </h4>

                        <div class="space-y-4">
                            <FormField label="Overlay Color">
                                <ColorPicker
                                    :model-value="columnStyle.overlayColor ?? ''"
                                    @update:model-value="handleStyleUpdate('overlayColor', $event)"
                                />
                            </FormField>

                            <FormField v-if="columnStyle.overlayColor" label="Overlay Opacity">
                                <div class="flex items-center gap-3">
                                    <Slider
                                        class="flex-1"
                                        :model-value="columnStyle.overlayOpacity ?? 50"
                                        :min="0"
                                        :max="100"
                                        :step="5"
                                        @update:model-value="handleStyleUpdate('overlayOpacity', $event)"
                                    />
                                    <span class="w-10 text-right text-sm text-foreground-muted">
                                        {{ columnStyle.overlayOpacity ?? 50 }}%
                                    </span>
                                </div>
                            </FormField>
                        </div>
                    </div>
                </div>
            </TabPanel>
        </Tabs>
    </div>
</template>
