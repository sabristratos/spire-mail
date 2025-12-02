<script setup lang="ts">
import { onMounted, provide, ref, watch } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Button, Modal, Input, Select, SelectOption, ColorPicker, FormField, Icon, Tooltip } from '@sabrenski/spire-ui-vue'
import { ArrowTurnBackwardIcon, ArrowTurnForwardIcon, ViewIcon, FloppyDiskIcon, Settings02Icon, ArrowLeft02Icon, Edit02Icon } from '@hugeicons/core-free-icons'
import EditorSidebar from './EditorSidebar.vue'
import EditorCanvas from './EditorCanvas.vue'
import EditorProperties from './EditorProperties.vue'
import type { EditorProps, EditorContext } from '../../types/editor'
import type { BlockType } from '../../types/blocks'
import {
    initializeStore,
    editorState,
    addBlock,
    updateBlock,
    removeBlock,
    moveBlock,
    duplicateBlock,
    selectBlock,
    undo,
    redo,
    canUndo,
    canRedo,
    exportDocument,
    getSelectedBlock,
    updateSettings,
    defaultSettings,
} from '../../stores/editorStore'

const props = defineProps<EditorProps>()

const emit = defineEmits<{
    save: [content: { version: string; blocks: unknown[] }, settings: unknown]
    preview: [content: { version: string; blocks: unknown[] }, settings: unknown]
    'metadata-click': []
}>()

onMounted(() => {
    const blocks = props.template?.content?.blocks ?? []
    const settings = props.template?.settings ?? undefined
    initializeStore(blocks, settings)
})

watch(
    () => props.template,
    (newTemplate) => {
        if (newTemplate) {
            const blocks = newTemplate.content?.blocks ?? []
            const settings = newTemplate.settings ?? undefined
            initializeStore(blocks, settings)
        }
    }
)

const selectedBlock = getSelectedBlock()

const showSettingsModal = ref(false)

const emailFonts = [
    { label: 'Arial', value: 'Arial, sans-serif', isGoogleFont: false },
    { label: 'Georgia', value: 'Georgia, serif', isGoogleFont: false },
    { label: 'Helvetica', value: 'Helvetica, sans-serif', isGoogleFont: false },
    { label: 'Times New Roman', value: '"Times New Roman", serif', isGoogleFont: false },
    { label: 'Trebuchet MS', value: '"Trebuchet MS", sans-serif', isGoogleFont: false },
    { label: 'Verdana', value: 'Verdana, sans-serif', isGoogleFont: false },
    { label: 'Roboto', value: 'Roboto, sans-serif', isGoogleFont: true, googleName: 'Roboto' },
    { label: 'Open Sans', value: '"Open Sans", sans-serif', isGoogleFont: true, googleName: 'Open+Sans' },
    { label: 'Lato', value: 'Lato, sans-serif', isGoogleFont: true, googleName: 'Lato' },
    { label: 'Montserrat', value: 'Montserrat, sans-serif', isGoogleFont: true, googleName: 'Montserrat' },
    { label: 'Source Sans Pro', value: '"Source Sans Pro", sans-serif', isGoogleFont: true, googleName: 'Source+Sans+Pro' },
]

const loadedFonts = new Set<string>()

function loadGoogleFont(fontValue: string): void {
    const font = emailFonts.find(f => f.value === fontValue)
    if (!font?.isGoogleFont || !font.googleName || loadedFonts.has(font.googleName)) return

    const link = document.createElement('link')
    link.href = `https://fonts.googleapis.com/css2?family=${font.googleName}:wght@400;600;700&display=swap`
    link.rel = 'stylesheet'
    document.head.appendChild(link)
    loadedFonts.add(font.googleName)
}

watch(
    () => editorState.value.settings.fontFamily,
    (fontFamily) => {
        if (fontFamily) loadGoogleFont(fontFamily)
    },
    { immediate: true }
)

const editorContext: EditorContext = {
    get blocks() {
        return editorState.value.blocks
    },
    get settings() {
        return editorState.value.settings
    },
    get selectedBlockId() {
        return editorState.value.selectedBlockId
    },
    selectBlock,
    addBlock: (type: string, index?: number) => addBlock(type as BlockType, index),
    updateBlock,
    removeBlock,
    moveBlock,
    duplicateBlock,
}

provide('emailEditor', editorContext)

function handleSave(): void {
    const content = exportDocument()
    emit('save', content, editorState.value.settings)
}

function handlePreview(): void {
    const content = exportDocument()
    emit('preview', content, editorState.value.settings)
}

function handleKeydown(e: KeyboardEvent): void {
    if (e.ctrlKey || e.metaKey) {
        if (e.key === 'z' && !e.shiftKey) {
            e.preventDefault()
            undo()
        } else if ((e.key === 'z' && e.shiftKey) || e.key === 'y') {
            e.preventDefault()
            redo()
        } else if (e.key === 's') {
            e.preventDefault()
            handleSave()
        }
    }

    if (e.key === 'Delete' || e.key === 'Backspace') {
        if (editorState.value.selectedBlockId && !isInputFocused()) {
            e.preventDefault()
            removeBlock(editorState.value.selectedBlockId)
        }
    }

    if (e.key === 'Escape') {
        selectBlock(null)
    }
}

function isInputFocused(): boolean {
    const active = document.activeElement
    if (!active) return false
    const tagName = active.tagName.toLowerCase()
    return tagName === 'input' || tagName === 'textarea' || (active as HTMLElement).isContentEditable
}
</script>

<template>
    <div
        class="grid h-screen bg-canvas"
        style="grid-template-columns: 280px 1fr 320px; grid-template-rows: auto 1fr"
        @keydown="handleKeydown"
        tabindex="0"
    >
        <header class="col-span-3 flex items-center justify-between border-b border-border bg-surface px-4 py-2">
            <div class="flex items-center gap-3">
                <Link
                    v-if="showBackLink && backLinkHref"
                    :href="backLinkHref"
                    class="flex items-center gap-1 text-sm text-foreground-muted hover:text-foreground"
                >
                    <Icon :name="ArrowLeft02Icon" size="sm" />
                    <span>Back</span>
                </Link>

                <div v-if="showBackLink && backLinkHref" class="h-6 w-px bg-border" />

                <div>
                    <h1 class="text-lg font-semibold text-foreground">
                        {{ template?.name ?? 'New Template' }}
                    </h1>
                    <span v-if="editorState.isDirty" class="text-sm text-foreground-muted">(unsaved)</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <Tooltip>
                    <Button variant="ghost" size="sm" icon-only :icon="ArrowTurnBackwardIcon" :disabled="!canUndo().value" aria-label="Undo" @click="undo" />
                    <template #content>
                        <div class="flex items-center gap-2">
                            <span>Undo</span>
                            <kbd class="px-1 bg-surface/50 rounded text-xs">Ctrl+Z</kbd>
                        </div>
                    </template>
                </Tooltip>

                <Tooltip>
                    <Button variant="ghost" size="sm" icon-only :icon="ArrowTurnForwardIcon" :disabled="!canRedo().value" aria-label="Redo" @click="redo" />
                    <template #content>
                        <div class="flex items-center gap-2">
                            <span>Redo</span>
                            <kbd class="px-1 bg-surface/50 rounded text-xs">Ctrl+Y</kbd>
                        </div>
                    </template>
                </Tooltip>

                <div class="mx-2 h-6 w-px bg-border" />

                <Tooltip v-if="template" content="Edit Template Details">
                    <Button
                        variant="ghost"
                        size="sm"
                        icon-only
                        :icon="Edit02Icon"
                        aria-label="Edit Template Details"
                        @click="emit('metadata-click')"
                    />
                </Tooltip>

                <Tooltip content="Email Settings">
                    <Button
                        variant="ghost"
                        size="sm"
                        icon-only
                        :icon="Settings02Icon"
                        aria-label="Email Settings"
                        @click="showSettingsModal = true"
                    />
                </Tooltip>

                <Button variant="secondary" size="sm" :icon="ViewIcon" @click="handlePreview">Preview</Button>

                <Tooltip>
                    <Button size="sm" :icon="FloppyDiskIcon" @click="handleSave">Save</Button>
                    <template #content>
                        <div class="flex items-center gap-2">
                            <span>Save</span>
                            <kbd class="px-1 bg-surface/50 rounded text-xs">Ctrl+S</kbd>
                        </div>
                    </template>
                </Tooltip>
            </div>
        </header>

        <EditorSidebar class="row-start-2 border-r border-border" :available-blocks="availableBlocks" />

        <EditorCanvas class="row-start-2 overflow-auto bg-subtle" />

        <EditorProperties class="row-start-2 border-l border-border" :block="selectedBlock" />

        <Modal v-model="showSettingsModal" title="Email Settings" size="sm">
            <div class="space-y-4">
                <FormField label="Font Family" description="Default font for the email">
                    <Select
                        :model-value="editorState.settings.fontFamily"
                        @update:model-value="(v: string) => updateSettings({ fontFamily: v })"
                    >
                        <SelectOption
                            v-for="font in emailFonts"
                            :key="font.value"
                            :value="font.value"
                        >
                            {{ font.label }}
                        </SelectOption>
                    </Select>
                </FormField>

                <FormField label="Background Color" description="Outer background color">
                    <ColorPicker
                        size="sm"
                        clearable
                        :model-value="editorState.settings.backgroundColor"
                        @update:model-value="(v: string) => updateSettings({ backgroundColor: v })"
                    />
                </FormField>

                <FormField label="Content Background" description="Inner content area background">
                    <ColorPicker
                        size="sm"
                        clearable
                        :model-value="editorState.settings.contentBackgroundColor"
                        @update:model-value="(v: string) => updateSettings({ contentBackgroundColor: v })"
                    />
                </FormField>

                <FormField label="Content Width" description="Width of email content (400-800px)">
                    <Input
                        type="number"
                        :model-value="editorState.settings.contentWidth"
                        :min="400"
                        :max="800"
                        @update:model-value="(v: number) => updateSettings({ contentWidth: Number(v) })"
                    />
                </FormField>
            </div>

            <template #footer>
                <Button variant="ghost" @click="updateSettings({ ...defaultSettings })">Restore Defaults</Button>
                <Button @click="showSettingsModal = false">Close</Button>
            </template>
        </Modal>
    </div>
</template>
