<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { Modal, Button, Icon, Spinner, Input, Tabs, TabList, Tab } from '@sabrenski/spire-ui-vue'
import {
    ComputerIcon,
    SmartPhone01Icon,
    RefreshIcon,
    MailSend01Icon,
} from '@hugeicons/core-free-icons'
import type { EmailBlock, EmailSettings } from '../../types/blocks'

interface Props {
    /** Controls modal visibility via v-model */
    modelValue: boolean
    /** Template ID for fetching preview */
    templateId: number | null
    /** Current editor blocks for preview */
    content: EmailBlock[]
    /** Current editor settings */
    settings: EmailSettings
    /** CSRF token for API requests */
    csrfToken?: string
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'update:modelValue': [value: boolean]
    'test-sent': [email: string]
}>()

type Viewport = 'desktop' | 'mobile'

const viewport = ref<Viewport>('desktop')
const previewHtml = ref('')
const isLoading = ref(false)
const error = ref<string | null>(null)

const showTestEmailForm = ref(false)
const testEmail = ref('')
const isSendingTest = ref(false)
const testEmailSuccess = ref<string | null>(null)
const testEmailError = ref<string | null>(null)

const viewportWidths: Record<Viewport, number> = {
    desktop: 600,
    mobile: 375,
}

const iframeWidth = computed(() => viewportWidths[viewport.value])

const csrfToken = computed(() => {
    if (props.csrfToken) return props.csrfToken
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? ''
})

watch(
    () => props.modelValue,
    (isOpen) => {
        if (isOpen && props.templateId) {
            fetchPreview()
        }
    }
)

async function fetchPreview(): Promise<void> {
    if (!props.templateId) {
        error.value = 'Template must be saved before preview'
        return
    }

    isLoading.value = true
    error.value = null

    try {
        const response = await fetch(`/admin/mail/templates/${props.templateId}/preview`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
            },
            body: JSON.stringify({
                data: {},
                content: props.content,
                settings: props.settings,
            }),
        })

        if (!response.ok) {
            throw new Error('Failed to fetch preview')
        }

        const result = await response.json()
        previewHtml.value = result.html
    } catch (e) {
        error.value = e instanceof Error ? e.message : 'Preview failed'
    } finally {
        isLoading.value = false
    }
}

async function sendTestEmail(): Promise<void> {
    if (!props.templateId || !testEmail.value) return

    isSendingTest.value = true
    testEmailSuccess.value = null
    testEmailError.value = null

    try {
        const response = await fetch(`/admin/mail/templates/${props.templateId}/send-test`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
            },
            body: JSON.stringify({
                email: testEmail.value,
                data: {},
            }),
        })

        const result = await response.json()

        if (result.success) {
            testEmailSuccess.value = result.message
            emit('test-sent', testEmail.value)
            testEmail.value = ''
            showTestEmailForm.value = false
        } else {
            testEmailError.value = result.message || 'Failed to send test email'
        }
    } catch (e) {
        testEmailError.value = e instanceof Error ? e.message : 'Failed to send test email'
    } finally {
        isSendingTest.value = false
    }
}

function onModalUpdate(value: boolean): void {
    if (!value) {
        showTestEmailForm.value = false
        testEmailSuccess.value = null
        testEmailError.value = null
    }
    emit('update:modelValue', value)
}

function handleClose(): void {
    onModalUpdate(false)
}
</script>

<template>
    <Modal
        :model-value="modelValue"
        title="Email Preview"
        size="xl"
        @update:model-value="onModalUpdate"
    >
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <span class="text-lg font-semibold text-foreground">Email Preview</span>

                <div class="flex items-center gap-3">
                    <Tabs v-model="viewport" variant="solid" size="sm" disable-container-query>
                        <TabList>
                            <Tab tab-key="desktop">
                                <Icon :name="ComputerIcon" size="sm" />
                                <span class="hidden sm:inline">Desktop</span>
                            </Tab>
                            <Tab tab-key="mobile">
                                <Icon :name="SmartPhone01Icon" size="sm" />
                                <span class="hidden sm:inline">Mobile</span>
                            </Tab>
                        </TabList>
                    </Tabs>

                    <Button
                        variant="ghost"
                        size="sm"
                        icon-only
                        :icon="RefreshIcon"
                        :disabled="isLoading"
                        aria-label="Refresh preview"
                        @click="fetchPreview"
                    />
                </div>
            </div>
        </template>

        <div class="min-h-[500px] bg-subtle p-4">
            <div v-if="isLoading" class="flex h-[500px] items-center justify-center">
                <Spinner size="lg" />
            </div>

            <div v-else-if="error" class="flex h-[500px] flex-col items-center justify-center gap-4 text-center">
                <Icon name="alert-circle" size="xl" class="text-danger" />
                <p class="text-foreground-muted">{{ error }}</p>
                <Button variant="secondary" size="sm" @click="fetchPreview">
                    Try Again
                </Button>
            </div>

            <div v-else-if="!templateId" class="flex h-[500px] flex-col items-center justify-center gap-2 text-center">
                <Icon name="save-01" size="xl" class="text-foreground-muted" />
                <p class="text-foreground-muted">Save your template first to preview</p>
            </div>

            <div v-else class="flex justify-center">
                <iframe
                    :srcdoc="previewHtml"
                    :style="{ width: `${iframeWidth}px` }"
                    class="h-[600px] rounded border border-border bg-white transition-all duration-300"
                    sandbox="allow-same-origin"
                    title="Email preview"
                />
            </div>
        </div>

        <template #footer>
            <div class="flex w-full items-center justify-between">
                <div class="flex items-center gap-2">
                    <template v-if="!showTestEmailForm">
                        <Button
                            v-if="templateId"
                            variant="secondary"
                            size="sm"
                            :icon="MailSend01Icon"
                            @click="showTestEmailForm = true"
                        >
                            Send Test Email
                        </Button>
                        <span v-if="testEmailSuccess" class="text-sm text-success">
                            {{ testEmailSuccess }}
                        </span>
                    </template>

                    <template v-else>
                        <form class="flex items-center gap-2" @submit.prevent="sendTestEmail">
                            <Input
                                v-model="testEmail"
                                type="email"
                                placeholder="test@example.com"
                                size="sm"
                                required
                                class="w-64"
                            />
                            <Button
                                type="submit"
                                size="sm"
                                :loading="isSendingTest"
                                :disabled="!testEmail"
                            >
                                Send
                            </Button>
                            <Button
                                type="button"
                                variant="ghost"
                                size="sm"
                                @click="showTestEmailForm = false"
                            >
                                Cancel
                            </Button>
                        </form>
                        <span v-if="testEmailError" class="text-sm text-danger">
                            {{ testEmailError }}
                        </span>
                    </template>
                </div>

                <Button @click="handleClose">Close</Button>
            </div>
        </template>
    </Modal>
</template>
