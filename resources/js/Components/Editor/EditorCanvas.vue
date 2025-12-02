<script setup lang="ts">
import { computed } from 'vue'
import { Icon } from '@sabrenski/spire-ui-vue'
import { Mail02Icon } from '@hugeicons/core-free-icons'
import CanvasBlock from '../Canvas/CanvasBlock.vue'
import CanvasDropZone from '../Canvas/CanvasDropZone.vue'
import { editorState } from '../../stores/editorStore'
import { useDragDrop } from '../../composables/useDragDrop'

const blocks = computed(() => editorState.value.blocks)
const settings = computed(() => editorState.value.settings)
const isDragging = computed(() => editorState.value.isDragging)

const { createDropZoneHandlers } = useDragDrop()
</script>

<template>
    <main class="@container flex h-full items-start justify-center overflow-auto p-6">
        <div
            class="email-canvas min-h-[400px] w-full max-w-[600px] rounded-lg shadow-lg @md:max-w-none"
            :style="{
                width: `${settings.contentWidth}px`,
                backgroundColor: settings.contentBackgroundColor,
                fontFamily: settings.fontFamily,
            }"
        >
            <div
                v-if="blocks.length === 0"
                class="relative min-h-[400px] p-4 @sm:p-8"
                v-bind="createDropZoneHandlers(0)"
            >
                <div
                    class="pointer-events-none flex min-h-[350px] flex-col items-center justify-center text-center transition-opacity"
                    :class="isDragging ? 'opacity-30' : 'opacity-100'"
                >
                    <div class="mb-4 rounded-full bg-muted/20 p-4">
                        <Icon :name="Mail02Icon" size="xl" class="text-foreground-muted" />
                    </div>
                    <h3 class="text-lg font-semibold text-foreground">No blocks yet</h3>
                    <p class="mt-1 max-w-xs text-sm text-foreground-muted">
                        Drag blocks from the sidebar to start building your email template
                    </p>
                </div>

                <div
                    v-if="isDragging"
                    class="absolute inset-4 flex items-center justify-center rounded-lg border-2 border-dashed border-primary bg-primary-subtle/50"
                >
                    <span class="text-sm font-medium text-primary">Drop block here</span>
                </div>
            </div>

            <div v-else>
                <CanvasDropZone :index="0" v-bind="createDropZoneHandlers(0)" />

                <template v-for="(block, index) in blocks" :key="block.id">
                    <CanvasBlock :block="block" :index="index" />
                    <CanvasDropZone :index="index + 1" v-bind="createDropZoneHandlers(index + 1)" />
                </template>
            </div>
        </div>
    </main>
</template>
