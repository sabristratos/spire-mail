<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { EmailEditor, PreviewModal } from '../../index'
import { Modal, Button, Input, Textarea, FormField, ToastContainer } from '@sabrenski/spire-ui-vue'
import type { TemplateData, BlockDefinition } from '../../types/editor'
import type { EmailBlock, EmailSettings } from '../../types/blocks'

interface Props {
    template: { data: TemplateData } | null
    availableBlocks: Record<string, BlockDefinition>
}

const props = defineProps<Props>()

const template = computed(() => props.template?.data ?? null)

const showMetadataModal = ref(!template.value)
const showPreviewModal = ref(false)
const isLoading = ref(false)

const editorContent = ref<{ version: string; blocks: EmailBlock[] } | null>(null)
const editorSettings = ref<EmailSettings | null>(null)

const metadata = ref({
    name: template.value?.name ?? '',
    subject: template.value?.subject ?? '',
    description: template.value?.description ?? '',
    preview_text: template.value?.preview_text ?? '',
})

function handleSave(content: { version: string; blocks: EmailBlock[] }, settings: EmailSettings): void {
    editorContent.value = content
    editorSettings.value = settings

    if (!template.value) {
        showMetadataModal.value = true
        return
    }

    saveTemplate(content, settings)
}

function saveTemplate(content: { version: string; blocks: EmailBlock[] }, settings: EmailSettings): void {
    if (!template.value) return

    isLoading.value = true

    router.put(`/admin/mail/templates/${template.value.id}`, {
        ...metadata.value,
        content,
        settings,
    }, {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            isLoading.value = false
        },
    })
}

function handleCreate(content: { version: string; blocks: EmailBlock[] }, settings: EmailSettings | null): void {
    isLoading.value = true

    router.post(
        '/admin/mail/templates',
        {
            ...metadata.value,
            content,
            settings,
        },
        {
            onFinish: () => {
                isLoading.value = false
                showMetadataModal.value = false
            },
        }
    )
}

function handlePreview(content: { version: string; blocks: EmailBlock[] }, settings: EmailSettings): void {
    editorContent.value = content
    editorSettings.value = settings
    showPreviewModal.value = true
}

function submitMetadata(): void {
    if (!metadata.value.name || !metadata.value.subject) {
        return
    }

    const content = editorContent.value ?? { version: '1.0', blocks: [] }
    handleCreate(content, editorSettings.value)
}
</script>

<template>
    <div class="h-screen bg-canvas">
        <ToastContainer placement="bottom-right" />

        <EmailEditor
            :template="template"
            :available-blocks="availableBlocks"
            show-back-link
            back-link-href="/admin/mail"
            @save="handleSave"
            @preview="handlePreview"
            @metadata-click="showMetadataModal = true"
        />

        <Modal v-model="showMetadataModal" title="Template Details" :closable="!!template">
            <div class="space-y-4">
                <FormField label="Template Name" required>
                    <Input
                        v-model="metadata.name"
                        placeholder="e.g., Welcome Email"
                    />
                </FormField>

                <FormField label="Email Subject" required>
                    <Input
                        v-model="metadata.subject"
                        placeholder="e.g., Welcome to {{app_name}}!"
                    />
                </FormField>

                <FormField label="Description">
                    <Textarea
                        v-model="metadata.description"
                        :rows="2"
                        placeholder="Optional description..."
                    />
                </FormField>

                <FormField label="Preview Text" description="Shown in email client preview pane">
                    <Input
                        v-model="metadata.preview_text"
                        placeholder="Text shown in email client preview..."
                    />
                </FormField>
            </div>

            <template #footer>
                <Button v-if="template" variant="secondary" @click="showMetadataModal = false">
                    Cancel
                </Button>
                <Button :loading="isLoading" @click="submitMetadata">
                    {{ template ? 'Save Details' : 'Create Template' }}
                </Button>
            </template>
        </Modal>

        <PreviewModal
            v-model="showPreviewModal"
            :template-id="template?.id ?? null"
            :content="editorContent?.blocks ?? []"
            :settings="editorSettings ?? { fontFamily: 'Arial, sans-serif', backgroundColor: '#f4f4f5', contentBackgroundColor: '#ffffff', contentWidth: 600 }"
        />
    </div>
</template>
