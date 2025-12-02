import { ref, readonly } from 'vue'

const announcement = ref('')
const priority = ref<'polite' | 'assertive'>('polite')

/**
 * Composable for managing screen reader announcements.
 * Uses an aria-live region to announce changes to assistive technologies.
 */
export function useAnnouncer() {
    /**
     * Announces a message to screen readers.
     * @param message - The message to announce
     * @param announcementPriority - 'polite' waits for user to be idle, 'assertive' interrupts immediately
     */
    function announce(message: string, announcementPriority: 'polite' | 'assertive' = 'polite'): void {
        announcement.value = ''
        priority.value = announcementPriority

        setTimeout(() => {
            announcement.value = message
        }, 100)
    }

    /**
     * Clears the current announcement.
     */
    function clear(): void {
        announcement.value = ''
    }

    return {
        announcement: readonly(announcement),
        priority: readonly(priority),
        announce,
        clear,
    }
}
