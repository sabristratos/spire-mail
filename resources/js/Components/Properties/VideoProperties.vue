<script setup lang="ts">
import { Input, Select, SelectOption, FormField } from '@sabrenski/spire-ui-vue'
import type { VideoBlockProps } from '../../types/blocks'
import { useBlockProperties } from '../../composables/useBlockProperties'
import { ALIGNMENT_OPTIONS, DEFAULT_PADDING } from '../../constants'
import PaddingControl from './PaddingControl.vue'

interface Props {
    blockProps: VideoBlockProps
}

const props = defineProps<Props>()

const emit = defineEmits<{
    update: [updates: Partial<VideoBlockProps>]
}>()

const { update, updatePadding, updateInt } = useBlockProperties<VideoBlockProps>(emit)

/**
 * Extract thumbnail from YouTube/Vimeo URL
 */
function fetchThumbnail(): void {
    const videoUrl = props.blockProps.videoUrl ?? ''

    const youtubeMatch = videoUrl.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([^&\n?#]+)/)
    if (youtubeMatch) {
        update('thumbnailUrl', `https://img.youtube.com/vi/${youtubeMatch[1]}/maxresdefault.jpg`)
        return
    }

    const vimeoMatch = videoUrl.match(/vimeo\.com\/(\d+)/)
    if (vimeoMatch) {
        update('thumbnailUrl', `https://vumbnail.com/${vimeoMatch[1]}.jpg`)
        return
    }
}
</script>

<template>
    <div class="space-y-4 p-4">
        <FormField label="Video URL" description="YouTube or Vimeo URL">
            <Input
                :model-value="props.blockProps.videoUrl ?? ''"
                placeholder="https://youtube.com/watch?v=..."
                @update:model-value="update('videoUrl', $event)"
            />
        </FormField>

        <FormField label="Thumbnail URL">
            <div class="flex gap-2">
                <Input
                    class="flex-1"
                    :model-value="props.blockProps.thumbnailUrl ?? ''"
                    placeholder="Auto-generated or custom URL"
                    @update:model-value="update('thumbnailUrl', $event)"
                />
                <button
                    type="button"
                    class="px-3 py-2 text-sm bg-primary text-on-emphasis rounded-md hover:bg-primary-hover"
                    @click="fetchThumbnail"
                >
                    Auto
                </button>
            </div>
        </FormField>

        <FormField label="Alt Text">
            <Input
                :model-value="props.blockProps.alt ?? 'Video'"
                placeholder="Video description"
                @update:model-value="update('alt', $event)"
            />
        </FormField>

        <FormField label="Width">
            <Input
                :model-value="props.blockProps.width ?? '100%'"
                placeholder="100% or 600px"
                @update:model-value="update('width', $event)"
            />
        </FormField>

        <FormField label="Alignment">
            <Select :model-value="props.blockProps.align ?? 'center'" @update:model-value="update('align', $event as any)">
                <SelectOption v-for="align in ALIGNMENT_OPTIONS.horizontal" :key="align.value" :value="align.value">
                    {{ align.label }}
                </SelectOption>
            </Select>
        </FormField>

        <FormField label="Border Radius">
            <Input
                type="number"
                :model-value="String(props.blockProps.borderRadius ?? 0)"
                min="0"
                max="50"
                @update:model-value="updateInt('borderRadius', $event as string, 0)"
            />
        </FormField>

        <PaddingControl
            :model-value="props.blockProps.padding ?? DEFAULT_PADDING"
            @update:model-value="updatePadding"
        />
    </div>
</template>
