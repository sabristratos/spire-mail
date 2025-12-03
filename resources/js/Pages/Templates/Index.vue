<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import DefaultLayout from '../../Components/Layouts/DefaultLayout.vue'

defineOptions({
    layout: DefaultLayout,
})
import {
    Table,
    Button,
    Badge,
    Switch,
    Icon,
    EmptyState,
    Modal,
    ToastContainer,
    Dropdown,
    DropdownMenu,
    DropdownItem,
    Heading,
    Text,
    Input,
    DatePicker,
    RadioGroup,
    Radio,
    useToast,
    useHugeicons,
} from '@sabrenski/spire-ui-vue'

useHugeicons()
import {
    Add01Icon,
    Mail02Icon,
    Edit02Icon,
    Delete02Icon,
    Copy01Icon,
    MoreVerticalIcon,
    Search01Icon,
    FilterIcon,
} from '@hugeicons/core-free-icons'

interface Template {
    id: number
    name: string
    slug: string
    subject: string
    description: string | null
    is_active: boolean
    block_count: number
    last_sent_at: string | null
    updated_at: string
}

interface DateRange {
    start: Date | null
    end: Date | null
}

interface Props {
    templates: {
        data: Template[]
        links: unknown[]
    }
}

const props = defineProps<Props>()
const page = usePage()
const toast = useToast()
const routePrefix = computed(() => (page.props.spireMailPrefix as string) || '/admin/mail')

const selectedKeys = ref<number[]>([])
const togglingStatus = ref<Set<number>>(new Set())

const deleteModal = ref(false)
const templateToDelete = ref<Template | null>(null)
const isDeleting = ref(false)

const bulkDeleteModal = ref(false)
const isDeletingBulk = ref(false)

const searchQuery = ref('')
const statusFilter = ref('all')
const dateRange = ref<DateRange>({ start: null, end: null })

const hasSelection = computed(() => selectedKeys.value.length > 0)
const selectedCount = computed(() => selectedKeys.value.length)

const filteredTemplates = computed(() => {
    let result = props.templates.data

    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase()
        result = result.filter(template =>
            template.name.toLowerCase().includes(query) ||
            template.subject.toLowerCase().includes(query) ||
            template.description?.toLowerCase().includes(query)
        )
    }

    if (statusFilter.value !== 'all') {
        const isActive = statusFilter.value === 'active'
        result = result.filter(template => template.is_active === isActive)
    }

    if (dateRange.value.start || dateRange.value.end) {
        result = result.filter(template => {
            const updatedAt = new Date(template.updated_at)
            if (dateRange.value.start && updatedAt < dateRange.value.start) {
                return false
            }
            if (dateRange.value.end) {
                const endOfDay = new Date(dateRange.value.end)
                endOfDay.setHours(23, 59, 59, 999)
                if (updatedAt > endOfDay) {
                    return false
                }
            }
            return true
        })
    }

    return result
})

const hasActiveFilters = computed(() => {
    return searchQuery.value || statusFilter.value !== 'all' || dateRange.value.start || dateRange.value.end
})

const columns = [
    { key: 'name', label: 'Name', sortable: true, minWidth: '200px' },
    { key: 'subject', label: 'Subject', sortable: true, minWidth: '180px' },
    { key: 'is_active', label: 'Status', sortable: true, width: '100px', align: 'center' as const },
    { key: 'block_count', label: 'Blocks', sortable: true, width: '80px', align: 'center' as const },
    { key: 'last_sent_at', label: 'Last Sent', sortable: true, width: '120px' },
    { key: 'updated_at', label: 'Updated', sortable: true, width: '120px' },
    { key: 'actions', label: '', width: '60px', align: 'center' as const },
]

watch(() => page.props.flash, (flash) => {
    const toastData = (flash as Record<string, unknown>)?.toast as { color?: string; title?: string; description?: string } | undefined
    if (toastData) {
        toast.show({
            color: toastData.color as 'success' | 'danger' | 'warning' | 'default',
            title: toastData.title,
            description: toastData.description,
        })
    }
}, { immediate: true, deep: true })

function formatDate(dateString: string): string {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
    })
}

function toggleStatus(template: Template): void {
    togglingStatus.value.add(template.id)
    router.patch(`${routePrefix.value}/templates/${template.id}/toggle-status`, {}, {
        preserveScroll: true,
        onFinish: () => togglingStatus.value.delete(template.id),
    })
}

function confirmDelete(template: Template): void {
    templateToDelete.value = template
    deleteModal.value = true
}

function handleDelete(): void {
    if (!templateToDelete.value) return

    isDeleting.value = true

    router.delete(`${routePrefix.value}/templates/${templateToDelete.value.id}`, {
        onFinish: () => {
            isDeleting.value = false
            deleteModal.value = false
            templateToDelete.value = null
        },
    })
}

function handleDuplicate(template: Template): void {
    toast.loading('Duplicating template...')
    router.post(`${routePrefix.value}/templates/${template.id}/duplicate`, {}, {
        onError: () => {
            toast.dismissAll()
            toast.danger('Failed to duplicate template')
        },
    })
}

function handleBulkDelete(): void {
    isDeletingBulk.value = true
    router.delete(`${routePrefix.value}/templates/bulk`, {
        data: { ids: selectedKeys.value },
        preserveScroll: true,
        onSuccess: () => {
            selectedKeys.value = []
            bulkDeleteModal.value = false
        },
        onFinish: () => {
            isDeletingBulk.value = false
        },
    })
}

function clearSelection(): void {
    selectedKeys.value = []
}

function clearFilters(): void {
    searchQuery.value = ''
    statusFilter.value = 'all'
    dateRange.value = { start: null, end: null }
}

function handleRowAction(key: string, template: Template): void {
    switch (key) {
        case 'edit':
            router.visit(`${routePrefix.value}/templates/${template.id}`)
            break
        case 'duplicate':
            handleDuplicate(template)
            break
        case 'delete':
            confirmDelete(template)
            break
    }
}
</script>

<template>
    <div>
        <ToastContainer placement="bottom-right" />

        <header class="border-b border-border bg-surface px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <Heading :level="1" size="xl">Email Templates</Heading>
                    <Text variant="subtle">Manage your email templates</Text>
                </div>
                <Link :href="`${routePrefix}/templates/create`">
                    <Button :icon="Add01Icon">
                        New Template
                    </Button>
                </Link>
            </div>
        </header>

        <div class="border-b border-border bg-surface px-6 py-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px] max-w-sm">
                    <Input
                        v-model="searchQuery"
                        :icon="Search01Icon"
                        type="search"
                        placeholder="Search templates..."
                        clearable
                        size="sm"
                    />
                </div>

                <div class="min-w-[200px]">
                    <DatePicker
                        v-model="dateRange"
                        mode="range"
                        placeholder="Filter by date..."
                        size="sm"
                        clearable
                    />
                </div>

                <RadioGroup
                    v-model="statusFilter"
                    variant="pill"
                    orientation="horizontal"
                    size="sm"
                >
                    <Radio value="all" label="All" />
                    <Radio value="active" label="Active" />
                    <Radio value="inactive" label="Inactive" />
                </RadioGroup>

                <Button
                    v-if="hasActiveFilters"
                    variant="ghost"
                    size="sm"
                    @click="clearFilters"
                >
                    Clear filters
                </Button>
            </div>

            <div v-if="hasActiveFilters" class="mt-3 flex items-center gap-2">
                <Icon :name="FilterIcon" size="xs" class="text-foreground-muted" />
                <Text variant="subtle" inline class="text-xs">
                    Showing {{ filteredTemplates.length }} of {{ props.templates.data.length }} templates
                </Text>
            </div>
        </div>

        <main class="p-6">
            <EmptyState
                v-if="props.templates.data.length === 0"
                :icon="Mail02Icon"
                title="No templates yet"
                description="Create your first email template to get started"
                intent="primary"
                class="w-full py-12"
            >
                <template #actions>
                    <Link :href="`${routePrefix}/templates/create`">
                        <Button>Create Template</Button>
                    </Link>
                </template>
            </EmptyState>

            <EmptyState
                v-else-if="filteredTemplates.length === 0"
                :icon="Search01Icon"
                title="No matching templates"
                description="Try adjusting your filters to find what you're looking for"
                class="w-full py-12"
            >
                <template #actions>
                    <Button variant="secondary" @click="clearFilters">Clear filters</Button>
                </template>
            </EmptyState>

            <Table
                v-else
                :data="(filteredTemplates as any)"
                :columns="columns"
                row-key="id"
                selection-mode="multiple"
                v-model:selected-keys="selectedKeys"
                hoverable
                client-side-sorting
            >
                <template #cell-name="{ row }">
                    <div class="min-w-0">
                        <Text variant="strong" class="truncate block">{{ row.name }}</Text>
                        <Text
                            v-if="row.description"
                            variant="subtle"
                            inline
                            class="text-xs truncate block"
                        >
                            {{ row.description }}
                        </Text>
                    </div>
                </template>

                <template #cell-subject="{ row }">
                    <Text variant="default" class="truncate block">{{ row.subject }}</Text>
                </template>

                <template #cell-is_active="{ row }">
                    <Switch
                        :model-value="row.is_active"
                        :loading="togglingStatus.has(row.id)"
                        :disabled="togglingStatus.has(row.id)"
                        size="sm"
                        color="success"
                        @change="toggleStatus(row)"
                    />
                </template>

                <template #cell-block_count="{ row }">
                    <Badge color="secondary" variant="flat" size="sm">{{ row.block_count }}</Badge>
                </template>

                <template #cell-last_sent_at="{ row }">
                    <Text variant="subtle">{{ row.last_sent_at ? formatDate(row.last_sent_at) : 'Never' }}</Text>
                </template>

                <template #cell-updated_at="{ row }">
                    <Text variant="subtle">{{ formatDate(row.updated_at) }}</Text>
                </template>

                <template #cell-actions="{ row }">
                    <Dropdown>
                        <Button variant="ghost" size="sm" icon-only aria-label="Actions">
                            <Icon :name="MoreVerticalIcon" size="sm" />
                        </Button>
                        <DropdownMenu>
                            <DropdownItem
                                item-key="edit"
                                :icon="Edit02Icon"
                                title="Edit"
                                @action="handleRowAction('edit', row)"
                            />
                            <DropdownItem
                                item-key="duplicate"
                                :icon="Copy01Icon"
                                title="Duplicate"
                                @action="handleRowAction('duplicate', row)"
                            />
                            <DropdownItem
                                item-key="delete"
                                :icon="Delete02Icon"
                                title="Delete"
                                color="danger"
                                @action="handleRowAction('delete', row)"
                            />
                        </DropdownMenu>
                    </Dropdown>
                </template>
            </Table>
        </main>

        <Transition
            enter-active-class="animate-slide-up"
            leave-active-class="animate-fade-out"
        >
            <div
                v-if="hasSelection"
                class="fixed bottom-6 left-1/2 -translate-x-1/2 z-20"
            >
                <div class="flex items-center gap-3 bg-elevated border border-border rounded-lg shadow-lg px-4 py-3">
                    <Badge color="primary" variant="flat" :content="selectedCount" />
                    <Text variant="subtle" inline>selected</Text>
                    <div class="w-px h-6 bg-border" />
                    <Button variant="danger" size="sm" @click="bulkDeleteModal = true">
                        Delete Selected
                    </Button>
                    <Button variant="ghost" size="sm" @click="clearSelection">
                        Clear
                    </Button>
                </div>
            </div>
        </Transition>

        <Modal v-model="deleteModal" title="Delete Template" size="sm">
            <Text>
                Are you sure you want to delete
                <Text inline variant="strong">{{ templateToDelete?.name }}</Text>?
                This action cannot be undone.
            </Text>

            <template #footer>
                <Button variant="secondary" @click="deleteModal = false">Cancel</Button>
                <Button variant="danger" :loading="isDeleting" @click="handleDelete">
                    Delete Template
                </Button>
            </template>
        </Modal>

        <Modal v-model="bulkDeleteModal" title="Delete Templates" size="sm">
            <Text>
                Are you sure you want to delete
                <Text inline variant="strong">{{ selectedCount }} templates</Text>?
                This action cannot be undone.
            </Text>

            <template #footer>
                <Button variant="secondary" @click="bulkDeleteModal = false">Cancel</Button>
                <Button variant="danger" :loading="isDeletingBulk" @click="handleBulkDelete">
                    Delete Templates
                </Button>
            </template>
        </Modal>
    </div>
</template>
