<script setup lang="ts">
import { ref, computed, type Component } from 'vue'

defineOptions({
    inheritAttrs: false,
})
import { Icon, Tabs, TabList, Tab, TabPanel, Modal, Button } from '@sabrenski/spire-ui-vue'
import type { BlockDefinition, BlockType } from '../../types/blocks'
import type { EmailTemplate } from '../../types/templates'
import { useDragDrop } from '../../composables/useDragDrop'
import { getBlockIcon } from '../../registry'
import { SECTION_TEMPLATES, LAYOUT_TEMPLATES, cloneBlocksWithNewIds } from '../../constants/templates'
import { addBlocksAtEnd } from '../../stores/editorStore'
import TagsPanel from './TagsPanel.vue'
import TagEditor from './TagEditor.vue'
import {
    ColumnInsertIcon,
    Image02Icon,
    TextIndentIcon,
    InsertBottomImageIcon,
    Share01Icon,
    SignatureIcon,
    NewsIcon,
    MarketingIcon,
    WavingHand01Icon,
    More03Icon,
} from '@hugeicons/core-free-icons'

interface GlobalTag {
    key: string
    label: string
    description: string
    example: string | null
    global: boolean
}

interface TemplateTag {
    key: string
    label: string
    description: string
    type: string
    required: boolean
    default: string | number | undefined
    example: string | number | undefined
}

interface Props {
    availableBlocks: Record<string, BlockDefinition>
    globalTags?: GlobalTag[]
    templateTags?: TemplateTag[]
}

interface Emits {
    (e: 'update:templateTags', tags: TemplateTag[]): void
}

const props = withDefaults(defineProps<Props>(), {
    globalTags: () => [],
    templateTags: () => [],
})

const emit = defineEmits<Emits>()

const { createPaletteDragHandlers } = useDragDrop()

const activeTab = ref('blocks')
const showReplaceModal = ref(false)
const pendingTemplate = ref<EmailTemplate | null>(null)

function getIconComponent(type: string): Component {
    return getBlockIcon(type as BlockType) ?? ColumnInsertIcon
}

const sectionTemplates = computed(() => SECTION_TEMPLATES)
const layoutTemplates = computed(() => LAYOUT_TEMPLATES)

function handleTemplateClick(template: EmailTemplate): void {
    if (template.category === 'layout') {
        pendingTemplate.value = template
        showReplaceModal.value = true
    } else {
        addSectionTemplate(template)
    }
}

function confirmReplaceLayout(): void {
    if (pendingTemplate.value) {
        loadLayoutTemplate(pendingTemplate.value)
    }
    showReplaceModal.value = false
    pendingTemplate.value = null
}

function cancelReplaceLayout(): void {
    showReplaceModal.value = false
    pendingTemplate.value = null
}

function loadLayoutTemplate(template: EmailTemplate): void {
    const blocks = cloneBlocksWithNewIds(template.blocks)
    addBlocksAtEnd(blocks, true)
}

function addSectionTemplate(template: EmailTemplate): void {
    const blocks = cloneBlocksWithNewIds(template.blocks)
    addBlocksAtEnd(blocks, false)
}

function getSectionIcon(sectionType?: string): Component {
    switch (sectionType) {
        case 'header':
            return More03Icon
        case 'hero':
            return Image02Icon
        case 'content':
            return TextIndentIcon
        case 'cta':
            return InsertBottomImageIcon
        case 'footer':
            return Share01Icon
        case 'signature':
            return SignatureIcon
        default:
            return ColumnInsertIcon
    }
}

function getLayoutIcon(id: string): Component {
    switch (id) {
        case 'layout-newsletter':
            return NewsIcon
        case 'layout-announcement':
            return MarketingIcon
        case 'layout-welcome':
            return WavingHand01Icon
        default:
            return ColumnInsertIcon
    }
}

const showTagEditor = ref(false)
const editingTag = ref<TemplateTag | null>(null)

function handleAddTag(): void {
    editingTag.value = null
    showTagEditor.value = true
}

function handleEditTag(tag: TemplateTag): void {
    editingTag.value = tag
    showTagEditor.value = true
}

function handleSaveTag(tag: TemplateTag): void {
    const currentTags = [...props.templateTags]

    if (editingTag.value) {
        const index = currentTags.findIndex(t => t.key === editingTag.value?.key)
        if (index !== -1) {
            currentTags[index] = tag
        }
    } else {
        currentTags.push(tag)
    }

    emit('update:templateTags', currentTags)
    editingTag.value = null
}

function handleDeleteTag(key: string): void {
    const currentTags = props.templateTags.filter(t => t.key !== key)
    emit('update:templateTags', currentTags)
    editingTag.value = null
}

function handleInsertTag(_tagKey: string): void {
    // Tag insertion handled by TagsPanel copy-to-clipboard functionality
}
</script>

<template>
    <aside class="flex h-full flex-col bg-surface" v-bind="$attrs">
        <Tabs v-model="activeTab" size="sm" :full-width="true" default-value="blocks">
            <TabList>
                <Tab tab-key="blocks">Blocks</Tab>
                <Tab tab-key="templates">Templates</Tab>
                <Tab tab-key="tags">Tags</Tab>
            </TabList>

            <TabPanel tab-key="blocks" class="flex-1 overflow-y-auto !p-0">
                <div class="p-4">
                    <p class="mb-3 text-xs text-foreground-muted">Drag blocks to the canvas</p>
                    <p id="sidebar-drag-instructions" class="sr-only">
                        Drag a block to the canvas, or press Enter to add it at the end.
                    </p>
                    <div class="grid grid-cols-2 gap-2" role="list" aria-label="Available blocks">
                        <div
                            v-for="(block, type) in availableBlocks"
                            :key="type"
                            v-bind="createPaletteDragHandlers(type as any)"
                            role="button"
                            tabindex="0"
                            :aria-label="`Drag ${block.label} block to canvas`"
                            aria-describedby="sidebar-drag-instructions"
                            class="flex cursor-grab flex-col items-center gap-2 rounded-lg border border-border bg-elevated p-3 transition-all duration-150 hover:border-primary hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 active:cursor-grabbing"
                        >
                            <Icon :name="getIconComponent(String(type))" size="md" class="text-foreground-muted" aria-hidden="true" />
                            <span class="text-xs font-medium text-foreground">{{ block.label }}</span>
                        </div>
                    </div>
                </div>
            </TabPanel>

            <TabPanel tab-key="templates" class="flex-1 overflow-y-auto !p-0">
                <div class="p-4 space-y-6">
                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-foreground-muted">
                            Sections
                        </h3>
                        <p class="mb-3 text-xs text-foreground-muted">Click to add</p>
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                v-for="template in sectionTemplates"
                                :key="template.id"
                                role="button"
                                tabindex="0"
                                :aria-label="`Add ${template.name} section`"
                                class="flex cursor-pointer flex-col items-center gap-2 rounded-lg border border-border bg-elevated p-3 transition-all duration-150 hover:border-primary hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                @click="handleTemplateClick(template)"
                                @keydown.enter="handleTemplateClick(template)"
                            >
                                <Icon :name="getSectionIcon(template.sectionType)" size="lg" class="text-foreground-muted" aria-hidden="true" />
                                <span class="text-xs font-medium text-foreground text-center">{{ template.name }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-foreground-muted">
                            Full Layouts
                        </h3>
                        <p class="mb-3 text-xs text-foreground-muted">Click to replace canvas</p>
                        <div class="space-y-2">
                            <div
                                v-for="template in layoutTemplates"
                                :key="template.id"
                                role="button"
                                tabindex="0"
                                :aria-label="`Load ${template.name} layout`"
                                class="flex cursor-pointer items-center gap-3 rounded-lg border border-border bg-elevated p-3 transition-all duration-150 hover:border-primary hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                @click="handleTemplateClick(template)"
                                @keydown.enter="handleTemplateClick(template)"
                            >
                                <Icon :name="getLayoutIcon(template.id)" size="lg" class="text-foreground-muted" aria-hidden="true" />
                                <div class="flex-1 min-w-0">
                                    <span class="block text-sm font-medium text-foreground">{{ template.name }}</span>
                                    <span class="block text-xs text-foreground-muted truncate">{{ template.description }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </TabPanel>

            <TabPanel tab-key="tags" class="flex-1 overflow-y-auto !p-0">
                <TagsPanel
                    :global-tags="globalTags"
                    :template-tags="templateTags"
                    @add-tag="handleAddTag"
                    @edit-tag="handleEditTag"
                    @insert-tag="handleInsertTag"
                />
            </TabPanel>
        </Tabs>
    </aside>

    <TagEditor
        v-model="showTagEditor"
        :tag="editingTag"
        @save="handleSaveTag"
        @delete="handleDeleteTag"
    />

    <Modal
        v-model="showReplaceModal"
        title="Replace Content"
        size="sm"
    >
        <p class="text-foreground-muted">
            This will replace all current content with the <strong class="text-foreground">{{ pendingTemplate?.name }}</strong> layout. This action cannot be undone.
        </p>

        <template #footer="{ close }">
            <Button variant="ghost" @click="cancelReplaceLayout">Cancel</Button>
            <Button color="danger" @click="confirmReplaceLayout">Replace</Button>
        </template>
    </Modal>
</template>
