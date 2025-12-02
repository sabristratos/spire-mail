<script setup lang="ts">
import { computed } from 'vue'
import { Icon } from '@sabrenski/spire-ui-vue'
import { PlayIcon, Video01Icon } from '@hugeicons/core-free-icons'
import type { VideoBlockProps } from '../../types/blocks'

interface Props {
    blockProps: VideoBlockProps
}

const props = defineProps<Props>()

const styles = computed(() => {
    const p = props.blockProps.padding ?? { top: 10, right: 0, bottom: 10, left: 0 }
    return {
        padding: `${p.top ?? 0}px ${p.right ?? 0}px ${p.bottom ?? 0}px ${p.left ?? 0}px`,
        textAlign: props.blockProps.align ?? 'center',
    }
})

const imageStyles = computed(() => ({
    width: props.blockProps.width ?? '100%',
    borderRadius: props.blockProps.borderRadius ? `${props.blockProps.borderRadius}px` : undefined,
}))

const hasThumbnail = computed(() => !!props.blockProps.thumbnailUrl?.trim())
const hasVideo = computed(() => !!props.blockProps.videoUrl?.trim())

/**
 * Extracts YouTube video ID from various URL formats
 */
function getYouTubeId(url: string): string | null {
    const patterns = [
        /(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/,
        /youtube\.com\/shorts\/([^&\n?#]+)/,
    ]
    for (const pattern of patterns) {
        const match = url.match(pattern)
        if (match) return match[1]
    }
    return null
}

/**
 * Extracts Vimeo video ID from URL
 */
function getVimeoId(url: string): string | null {
    const match = url.match(/vimeo\.com\/(\d+)/)
    return match ? match[1] : null
}

/**
 * Generate thumbnail URL from video URL
 */
const autoThumbnail = computed(() => {
    const videoUrl = props.blockProps.videoUrl ?? ''

    const youtubeId = getYouTubeId(videoUrl)
    if (youtubeId) {
        return `https://img.youtube.com/vi/${youtubeId}/maxresdefault.jpg`
    }

    const vimeoId = getVimeoId(videoUrl)
    if (vimeoId) {
        return `https://vumbnail.com/${vimeoId}.jpg`
    }

    return null
})

const displayThumbnail = computed(() => props.blockProps.thumbnailUrl || autoThumbnail.value)
</script>

<template>
    <div class="video-block" :style="styles">
        <div v-if="!hasVideo" class="placeholder">
            <Icon :name="Video01Icon" size="lg" class="text-foreground-muted" />
            <span class="text-sm text-foreground-muted">Video Block</span>
        </div>
        <div v-else class="video-container" :style="{ display: 'inline-block', position: 'relative' }">
            <img
                v-if="displayThumbnail"
                :src="displayThumbnail"
                :alt="blockProps.alt ?? 'Video'"
                :style="imageStyles"
                class="video-thumbnail"
            />
            <div v-else class="thumbnail-placeholder" :style="imageStyles">
                <Icon :name="Video01Icon" size="xl" class="text-foreground-muted" />
            </div>
            <div class="play-overlay">
                <div class="play-button">
                    <Icon :name="PlayIcon" size="lg" class="text-white" />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.video-block {
    width: 100%;
}

.placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 32px;
    border: 1px dashed var(--color-border);
    border-radius: 4px;
    background: var(--color-surface);
}

.video-container {
    position: relative;
    display: inline-block;
}

.video-thumbnail {
    display: block;
    max-width: 100%;
    height: auto;
    object-fit: cover;
}

.thumbnail-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    background: var(--color-surface-hover);
    border: 1px solid var(--color-border);
}

.play-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.3);
    transition: background 0.2s;
}

.play-button {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.7);
    transition: transform 0.2s;
}

.video-container:hover .play-overlay {
    background: rgba(0, 0, 0, 0.4);
}

.video-container:hover .play-button {
    transform: scale(1.1);
}
</style>
