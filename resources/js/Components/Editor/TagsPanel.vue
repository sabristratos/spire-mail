<script setup lang="ts">
import { ref, computed } from 'vue'
import { Icon, Input, Button, Badge, Tooltip } from '@sabrenski/spire-ui-vue'
import { Copy01Icon, Edit02Icon, Add01Icon, Search01Icon, InformationCircleIcon } from '@hugeicons/core-free-icons'

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
    globalTags: GlobalTag[]
    templateTags: TemplateTag[]
}

interface Emits {
    (e: 'add-tag'): void
    (e: 'edit-tag', tag: TemplateTag): void
    (e: 'insert-tag', tagKey: string): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

function formatTagSyntax(key: string): string {
    return '{{' + key + '}}'
}

const searchQuery = ref('')
const copiedTag = ref<string | null>(null)

const filteredGlobalTags = computed(() => {
    if (!searchQuery.value) return props.globalTags
    const query = searchQuery.value.toLowerCase()
    return props.globalTags.filter(
        tag => tag.key.toLowerCase().includes(query) || tag.label.toLowerCase().includes(query)
    )
})

const filteredTemplateTags = computed(() => {
    if (!searchQuery.value) return props.templateTags
    const query = searchQuery.value.toLowerCase()
    return props.templateTags.filter(
        tag => tag.key.toLowerCase().includes(query) || tag.label.toLowerCase().includes(query)
    )
})

function copyTag(key: string): void {
    const tagSyntax = `{{${key}}}`
    navigator.clipboard.writeText(tagSyntax)
    copiedTag.value = key
    setTimeout(() => {
        copiedTag.value = null
    }, 2000)
}

function handleInsertTag(key: string): void {
    emit('insert-tag', key)
}

function handleAddTag(): void {
    emit('add-tag')
}

function handleEditTag(tag: TemplateTag): void {
    emit('edit-tag', tag)
}

const syntaxHelp = [
    { syntax: '{{tag|default:value}}', description: 'Fallback value' },
    { syntax: '{{tag|date:d/m/Y}}', description: 'Format date' },
    { syntax: '{{tag|currency:EUR}}', description: 'Format currency' },
    { syntax: '{{tag|uppercase}}', description: 'Uppercase text' },
    { syntax: '{{#if tag}}...{{/if}}', description: 'Conditional' },
]
</script>

<template>
    <div class="flex h-full flex-col">
        <div class="p-4 border-b border-border">
            <Input
                v-model="searchQuery"
                placeholder="Search tags..."
                size="sm"
                class="w-full"
            >
                <template #prefix>
                    <Icon :name="Search01Icon" size="sm" class="text-foreground-muted" />
                </template>
            </Input>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-6">
            <div v-if="filteredGlobalTags.length > 0">
                <h3 class="mb-3 text-xs font-semibold uppercase tracking-wide text-foreground-muted">
                    Global Tags
                </h3>
                <div class="space-y-2">
                    <div
                        v-for="tag in filteredGlobalTags"
                        :key="tag.key"
                        class="group rounded-lg border border-border bg-elevated p-3 transition-colors hover:border-primary"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <code class="text-xs font-mono text-primary">{{ formatTagSyntax(tag.key) }}</code>
                                <p class="mt-1 text-xs text-foreground">{{ tag.label }}</p>
                                <p v-if="tag.description" class="mt-0.5 text-xs text-foreground-muted truncate">
                                    {{ tag.description }}
                                </p>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Tooltip content="Copy tag">
                                    <Button
                                        variant="ghost"
                                        size="xs"
                                        icon-only
                                        :aria-label="`Copy ${tag.key} tag`"
                                        @click="copyTag(tag.key)"
                                    >
                                        <Icon :name="Copy01Icon" size="sm" />
                                    </Button>
                                </Tooltip>
                            </div>
                        </div>
                        <div v-if="copiedTag === tag.key" class="mt-2">
                            <Badge color="success" size="sm">Copied!</Badge>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-foreground-muted">
                        Template Tags
                    </h3>
                    <Button
                        variant="ghost"
                        size="xs"
                        @click="handleAddTag"
                    >
                        <Icon :name="Add01Icon" size="sm" />
                        Add
                    </Button>
                </div>

                <div v-if="filteredTemplateTags.length > 0" class="space-y-2">
                    <div
                        v-for="tag in filteredTemplateTags"
                        :key="tag.key"
                        class="group rounded-lg border border-border bg-elevated p-3 transition-colors hover:border-primary"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <code class="text-xs font-mono text-primary">{{ formatTagSyntax(tag.key) }}</code>
                                    <Badge v-if="tag.required" color="danger" size="sm">Required</Badge>
                                </div>
                                <p class="mt-1 text-xs text-foreground">{{ tag.label }}</p>
                                <p v-if="tag.description" class="mt-0.5 text-xs text-foreground-muted truncate">
                                    {{ tag.description }}
                                </p>
                            </div>
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Tooltip content="Edit tag">
                                    <Button
                                        variant="ghost"
                                        size="xs"
                                        icon-only
                                        :aria-label="`Edit ${tag.key} tag`"
                                        @click="handleEditTag(tag)"
                                    >
                                        <Icon :name="Edit02Icon" size="sm" />
                                    </Button>
                                </Tooltip>
                                <Tooltip content="Copy tag">
                                    <Button
                                        variant="ghost"
                                        size="xs"
                                        icon-only
                                        :aria-label="`Copy ${tag.key} tag`"
                                        @click="copyTag(tag.key)"
                                    >
                                        <Icon :name="Copy01Icon" size="sm" />
                                    </Button>
                                </Tooltip>
                            </div>
                        </div>
                        <div v-if="copiedTag === tag.key" class="mt-2">
                            <Badge color="success" size="sm">Copied!</Badge>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-6">
                    <p class="text-sm text-foreground-muted mb-3">No template tags defined</p>
                    <Button variant="outline" size="sm" @click="handleAddTag">
                        <Icon :name="Add01Icon" size="sm" />
                        Add First Tag
                    </Button>
                </div>
            </div>

            <div class="border-t border-border pt-4">
                <div class="flex items-center gap-2 mb-3">
                    <Icon :name="InformationCircleIcon" size="sm" class="text-foreground-muted" />
                    <h3 class="text-xs font-semibold uppercase tracking-wide text-foreground-muted">
                        Syntax Help
                    </h3>
                </div>
                <div class="space-y-2">
                    <div
                        v-for="item in syntaxHelp"
                        :key="item.syntax"
                        class="flex items-center justify-between text-xs"
                    >
                        <code class="font-mono text-foreground-muted">{{ item.syntax }}</code>
                        <span class="text-foreground-muted">{{ item.description }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
