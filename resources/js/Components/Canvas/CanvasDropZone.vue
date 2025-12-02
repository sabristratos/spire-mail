<script setup lang="ts">
import { computed } from 'vue'
import { editorState } from '../../stores/editorStore'

defineOptions({ inheritAttrs: false })

interface Props {
    index: number
    isOnlyZone?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    isOnlyZone: false,
})

const isDragging = computed(() => editorState.value.isDragging)
const isActive = computed(() => editorState.value.dropTargetIndex === props.index)
const isTargetingNestedArea = computed(() => editorState.value.dropTargetColumnId !== null)
const isDraggingInRow = computed(() => editorState.value.isDraggingInRow)
const shouldShow = computed(() => isDragging.value && !isTargetingNestedArea.value && !isDraggingInRow.value)
</script>

<template>
    <div
        v-bind="$attrs"
        :class="[
            'transition-all duration-200 rounded-sm',
            shouldShow ? 'h-4' : 'h-1',
            isActive && !isTargetingNestedArea
                ? 'h-12 bg-primary-subtle border-2 border-dashed border-primary mx-0'
                : shouldShow
                  ? 'bg-border/30 mx-4'
                  : 'bg-transparent',
            isOnlyZone && shouldShow && 'min-h-[200px] flex items-center justify-center',
        ]"
    >
        <span v-if="isOnlyZone && isActive && !isTargetingNestedArea" class="text-sm font-medium text-primary">
            Drop block here
        </span>
    </div>
</template>
