<script setup lang="ts">
import { computed } from 'vue'
import { EmptyState, Icon } from '@sabrenski/spire-ui-vue'
import { Cursor01Icon } from '@hugeicons/core-free-icons'
import type { EmailBlock } from '../../types/blocks'
import { updateBlock } from '../../stores/editorStore'
import { getPropertiesComponent, isWrapperBlock as checkIsWrapper } from '../../registry'
import { useBlockSelection } from '../../composables/useBlockSelection'
import ColumnProperties from '../Properties/ColumnProperties.vue'

interface Props {
    block: EmailBlock | null
}

const props = defineProps<Props>()

const {
    selectedColumnId,
    selectedColumnRowId,
    selectedColumn,
    selectedColumnIndex,
} = useBlockSelection()

const propertiesComponent = computed(() => {
    if (!props.block) return null
    return getPropertiesComponent(props.block.type)
})

const isWrapper = computed(() => props.block ? checkIsWrapper(props.block.type) : false)

const headerTitle = computed(() => {
    if (selectedColumnId.value && selectedColumn.value) {
        return `Column ${selectedColumnIndex.value + 1}`
    }
    if (props.block) {
        return `${props.block.type.charAt(0).toUpperCase() + props.block.type.slice(1)} block`
    }
    return 'Select a block'
})

function handleUpdate(updates: Partial<Record<string, unknown>>): void {
    if (props.block) {
        updateBlock(props.block.id, updates)
    }
}
</script>

<template>
    <aside class="flex h-full flex-col bg-surface">
        <header class="border-b border-border px-4 py-3">
            <h2 class="text-sm font-semibold text-foreground">Properties</h2>
            <p class="text-xs text-foreground-muted">
                {{ headerTitle }}
            </p>
        </header>

        <div class="flex-1 overflow-y-auto">
            <ColumnProperties
                v-if="selectedColumnId && selectedColumn && selectedColumnRowId"
                :column="selectedColumn"
                :row-id="selectedColumnRowId"
                :column-index="selectedColumnIndex"
            />

            <template v-else-if="block && propertiesComponent">
                <component
                    :is="propertiesComponent"
                    :block-props="block.props"
                    :block-id="isWrapper ? block.id : undefined"
                    @update="handleUpdate"
                />
            </template>

            <EmptyState
                v-else
                title="No block selected"
                description="Click on a block or column to edit its properties"
                class="m-4"
            >
                <template #icon>
                    <Icon :name="Cursor01Icon" size="lg" class="text-foreground-muted" />
                </template>
            </EmptyState>
        </div>
    </aside>
</template>
