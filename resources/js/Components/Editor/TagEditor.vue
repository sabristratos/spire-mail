<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Modal, Button, Input, Textarea, Select, Checkbox, FormField } from '@sabrenski/spire-ui-vue'

interface TemplateTag {
    key: string
    label: string
    description: string
    type: string
    required: boolean
    default: unknown
    example: string | null
}

interface Props {
    modelValue: boolean
    tag?: TemplateTag | null
}

interface Emits {
    (e: 'update:modelValue', value: boolean): void
    (e: 'save', tag: TemplateTag): void
    (e: 'delete', key: string): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const isOpen = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
})

const isEditing = computed(() => !!props.tag)

const form = ref<TemplateTag>({
    key: '',
    label: '',
    description: '',
    type: 'string',
    required: false,
    default: null,
    example: null,
})

const typeOptions = [
    { value: 'string', label: 'Text' },
    { value: 'number', label: 'Number' },
    { value: 'date', label: 'Date' },
    { value: 'url', label: 'URL' },
    { value: 'boolean', label: 'Boolean' },
]

watch(() => props.tag, (newTag) => {
    if (newTag) {
        form.value = { ...newTag }
    } else {
        resetForm()
    }
}, { immediate: true })

watch(isOpen, (open) => {
    if (!open) {
        resetForm()
    }
})

function resetForm(): void {
    form.value = {
        key: '',
        label: '',
        description: '',
        type: 'string',
        required: false,
        default: null,
        example: null,
    }
}

function handleSave(): void {
    if (!form.value.key.trim()) return

    emit('save', {
        ...form.value,
        key: form.value.key.trim(),
        label: form.value.label.trim() || humanizeKey(form.value.key),
        description: form.value.description.trim(),
        example: form.value.example?.trim() || null,
    })

    isOpen.value = false
}

function handleDelete(): void {
    if (props.tag) {
        emit('delete', props.tag.key)
        isOpen.value = false
    }
}

function humanizeKey(key: string): string {
    return key
        .replace(/[._-]/g, ' ')
        .replace(/\b\w/g, (char) => char.toUpperCase())
}

function formatKeyInput(event: Event): void {
    const input = event.target as HTMLInputElement
    form.value.key = input.value
        .toLowerCase()
        .replace(/\s+/g, '_')
        .replace(/[^a-z0-9_.]/g, '')
}
</script>

<template>
    <Modal
        v-model="isOpen"
        :title="isEditing ? 'Edit Tag' : 'Add Tag'"
        size="md"
    >
        <form class="space-y-4" @submit.prevent="handleSave">
            <FormField label="Key" required hint="Use dot notation for nested data (e.g., user.name)">
                <Input
                    v-model="form.key"
                    placeholder="e.g., user.name"
                    :disabled="isEditing"
                    @input="formatKeyInput"
                />
            </FormField>

            <FormField label="Label" hint="Human-readable name shown in the editor">
                <Input
                    v-model="form.label"
                    :placeholder="form.key ? humanizeKey(form.key) : 'e.g., User Name'"
                />
            </FormField>

            <FormField label="Description" hint="Explain what this tag represents">
                <Textarea
                    v-model="form.description"
                    placeholder="e.g., The full name of the user"
                    :rows="2"
                />
            </FormField>

            <div class="grid grid-cols-2 gap-4">
                <FormField label="Type">
                    <Select
                        v-model="form.type"
                        :options="typeOptions"
                    />
                </FormField>

                <FormField label="Example Value" hint="Shown in preview">
                    <Input
                        v-model="form.example"
                        placeholder="e.g., John Doe"
                    />
                </FormField>
            </div>

            <FormField label="Default Value" hint="Used when no value is provided">
                <Input
                    v-model="form.default"
                    placeholder="e.g., Valued Customer"
                />
            </FormField>

            <div class="flex items-center gap-2">
                <Checkbox
                    v-model="form.required"
                    label="Required"
                />
                <span class="text-xs text-foreground-muted">
                    Will show a warning if not provided when sending
                </span>
            </div>
        </form>

        <template #footer>
            <div class="flex items-center justify-between w-full">
                <div>
                    <Button
                        v-if="isEditing"
                        variant="ghost"
                        color="danger"
                        @click="handleDelete"
                    >
                        Delete
                    </Button>
                </div>
                <div class="flex items-center gap-2">
                    <Button variant="ghost" @click="isOpen = false">
                        Cancel
                    </Button>
                    <Button
                        color="primary"
                        :disabled="!form.key.trim()"
                        @click="handleSave"
                    >
                        {{ isEditing ? 'Save' : 'Add Tag' }}
                    </Button>
                </div>
            </div>
        </template>
    </Modal>
</template>
