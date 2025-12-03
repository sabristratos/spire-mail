<script setup lang="ts">
import { computed } from 'vue'
import { Input, Select, SelectOption, FormField, Slider, Switch } from '@sabrenski/spire-ui-vue'
import type { SocialIconsBlockProps, SocialLink, SocialPlatform } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { ALIGNMENT_OPTIONS, DEFAULT_PADDING } from '../../constants'
import ColorInput from './ColorInput.vue'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: SocialIconsBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<SocialIconsBlockProps>]
}>()

const { update, updatePadding } = useBlockProperties<SocialIconsBlockProps>(emit)

const ICON_SIZES = [
    { value: 'sm', label: 'Small (24px)' },
    { value: 'md', label: 'Medium (32px)' },
    { value: 'lg', label: 'Large (40px)' },
]

const ICON_STYLES = [
    { value: 'filled', label: 'Filled' },
    { value: 'outline', label: 'Outline' },
    { value: 'circle', label: 'Circle' },
]

const platformLabels: Record<SocialPlatform, string> = {
    facebook: 'Facebook',
    twitter: 'X (Twitter)',
    instagram: 'Instagram',
    linkedin: 'LinkedIn',
    youtube: 'YouTube',
    tiktok: 'TikTok',
    pinterest: 'Pinterest',
    email: 'Email',
}

const platformPlaceholders: Record<SocialPlatform, string> = {
    facebook: 'https://facebook.com/...',
    twitter: 'https://x.com/...',
    instagram: 'https://instagram.com/...',
    linkedin: 'https://linkedin.com/in/...',
    youtube: 'https://youtube.com/@...',
    tiktok: 'https://tiktok.com/@...',
    pinterest: 'https://pinterest.com/...',
    email: 'mailto:email@example.com',
}

const links = computed(() => props.blockProps.links ?? [])

function updateLink(index: number, field: keyof SocialLink, value: string | boolean): void {
    const newLinks = [...links.value]
    newLinks[index] = { ...newLinks[index], [field]: value }
    update('links', newLinks)
}
</script>

<template>
    <div class="space-y-4 p-4">
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-foreground">Social Links</h3>
            <div
                v-for="(link, index) in links"
                :key="link.platform"
                class="rounded-lg border border-border p-3 space-y-2"
            >
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium">{{ platformLabels[link.platform] }}</span>
                    <Switch
                        :model-value="link.enabled"
                        size="sm"
                        @update:model-value="updateLink(index, 'enabled', $event)"
                    />
                </div>
                <Input
                    v-if="link.enabled"
                    :model-value="link.url"
                    :placeholder="platformPlaceholders[link.platform]"
                    size="sm"
                    @update:model-value="updateLink(index, 'url', String($event))"
                />
            </div>
        </div>

        <FormField label="Icon Size">
            <Select :model-value="props.blockProps.iconSize ?? 'md'" @update:model-value="update('iconSize', $event as any)">
                <SelectOption v-for="size in ICON_SIZES" :key="size.value" :value="size.value">
                    {{ size.label }}
                </SelectOption>
            </Select>
        </FormField>

        <FormField label="Icon Style">
            <Select :model-value="props.blockProps.iconStyle ?? 'filled'" @update:model-value="update('iconStyle', $event as any)">
                <SelectOption v-for="style in ICON_STYLES" :key="style.value" :value="style.value">
                    {{ style.label }}
                </SelectOption>
            </Select>
        </FormField>

        <ColorInput
            label="Icon Color"
            description="Leave empty for platform colors"
            :model-value="props.blockProps.iconColor ?? ''"
            @update:model-value="update('iconColor', $event)"
        />

        <FormField label="Alignment">
            <Select :model-value="props.blockProps.align ?? 'center'" @update:model-value="update('align', $event as any)">
                <SelectOption v-for="align in ALIGNMENT_OPTIONS.horizontal" :key="align.value" :value="align.value">
                    {{ align.label }}
                </SelectOption>
            </Select>
        </FormField>

        <FormField label="Gap">
            <Slider
                :model-value="props.blockProps.gap ?? 12"
                :min="4"
                :max="32"
                :step="2"
                @update:model-value="update('gap', Number($event))"
            />
            <span class="text-xs text-foreground-muted">{{ props.blockProps.gap ?? 12 }}px</span>
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
