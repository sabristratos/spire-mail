<script setup lang="ts">
import { ref, computed } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'
import { Button, Input, Textarea, FormField, Card, ToastContainer, Icon } from '@sabrenski/spire-ui-vue'
import { ArrowLeft02Icon } from '@hugeicons/core-free-icons'
import DefaultLayout from '../../Components/Layouts/DefaultLayout.vue'

defineOptions({
    layout: DefaultLayout,
})

const page = usePage()
const routePrefix = computed(() => (page.props.spireMailPrefix as string) || '/admin/mail')

const isLoading = ref(false)

const form = ref({
    name: '',
    subject: '',
    description: '',
    preview_text: '',
})

const errors = ref<Record<string, string>>({})

function handleSubmit(): void {
    errors.value = {}

    if (!form.value.name.trim()) {
        errors.value.name = 'Template name is required'
    }

    if (!form.value.subject.trim()) {
        errors.value.subject = 'Email subject is required'
    }

    if (Object.keys(errors.value).length > 0) {
        return
    }

    isLoading.value = true

    router.post(`${routePrefix.value}/templates`, {
        name: form.value.name,
        subject: form.value.subject,
        description: form.value.description || null,
        preview_text: form.value.preview_text || null,
        content: { version: '1.0', blocks: [] },
        settings: {
            fontFamily: 'Arial, sans-serif',
            backgroundColor: '#f4f4f5',
            contentBackgroundColor: '#ffffff',
            contentWidth: 600,
        },
    }, {
        onError: (responseErrors) => {
            errors.value = responseErrors as Record<string, string>
        },
        onFinish: () => {
            isLoading.value = false
        },
    })
}
</script>

<template>
    <div>
        <ToastContainer placement="bottom-right" />

        <header class="border-b border-border bg-surface px-6 py-4">
            <div class="flex items-center gap-4">
                <Link
                    :href="routePrefix"
                    class="flex items-center gap-1 text-sm text-foreground-muted hover:text-foreground"
                >
                    <Icon :name="ArrowLeft02Icon" size="sm" />
                    <span>Back to Templates</span>
                </Link>

                <div class="h-6 w-px bg-border" />

                <h1 class="text-2xl font-bold text-foreground">Create Template</h1>
            </div>
        </header>

        <main class="mx-auto max-w-2xl p-6">
            <Card>
                <form class="space-y-6 p-6" @submit.prevent="handleSubmit">
                    <FormField
                        label="Template Name"
                        description="Internal name to identify this template"
                        required
                        :error="errors.name"
                    >
                        <Input
                            v-model="form.name"
                            placeholder="e.g., Welcome Email"
                            :has-error="!!errors.name"
                        />
                    </FormField>

                    <FormField
                        label="Email Subject"
                        description="Subject line recipients will see. Supports merge tags like {{name}}"
                        required
                        :error="errors.subject"
                    >
                        <Input
                            v-model="form.subject"
                            placeholder="e.g., Welcome to {{app_name}}!"
                            :has-error="!!errors.subject"
                        />
                    </FormField>

                    <FormField
                        label="Description"
                        description="Optional notes about this template"
                        :error="errors.description"
                    >
                        <Textarea
                            v-model="form.description"
                            :rows="3"
                            placeholder="Describe when this template is used..."
                        />
                    </FormField>

                    <FormField
                        label="Preview Text"
                        description="Text shown after the subject in email client previews"
                        :error="errors.preview_text"
                    >
                        <Input
                            v-model="form.preview_text"
                            placeholder="A short preview of the email content..."
                        />
                    </FormField>

                    <div class="flex items-center justify-end gap-3 border-t border-border pt-6">
                        <Link :href="routePrefix">
                            <Button type="button" variant="secondary">Cancel</Button>
                        </Link>
                        <Button type="submit" :loading="isLoading">
                            Create Template
                        </Button>
                    </div>
                </form>
            </Card>
        </main>
    </div>
</template>
