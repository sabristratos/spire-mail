import type { SpacingValue } from '../types/blocks'

/**
 * Composable for managing block property updates in Properties components.
 * Reduces boilerplate by providing standardized update functions.
 */
export function useBlockProperties<T extends Record<string, unknown>>(
    emit: (event: 'update', updates: Partial<T>) => void
) {
    /**
     * Update a single property on the block.
     */
    function update<K extends keyof T>(key: K, value: T[K]): void {
        emit('update', { [key]: value } as Partial<T>)
    }

    /**
     * Update the padding property.
     */
    function updatePadding(value: SpacingValue): void {
        emit('update', { padding: value } as Partial<T>)
    }

    /**
     * Update multiple properties at once.
     */
    function updateMultiple(updates: Partial<T>): void {
        emit('update', updates)
    }

    /**
     * Create an update handler for a specific property.
     * Useful for inline event handlers.
     */
    function createUpdater<K extends keyof T>(key: K) {
        return (value: T[K]) => update(key, value)
    }

    /**
     * Parse and update a numeric property from string input.
     */
    function updateNumber<K extends keyof T>(
        key: K,
        value: string | number,
        defaultValue: number = 0
    ): void {
        const parsed = typeof value === 'string' ? parseFloat(value) : value
        emit('update', { [key]: isNaN(parsed) ? defaultValue : parsed } as Partial<T>)
    }

    /**
     * Parse and update an integer property from string input.
     */
    function updateInt<K extends keyof T>(
        key: K,
        value: string | number,
        defaultValue: number = 0
    ): void {
        const parsed = typeof value === 'string' ? parseInt(value, 10) : value
        emit('update', { [key]: isNaN(parsed) ? defaultValue : parsed } as Partial<T>)
    }

    return {
        update,
        updatePadding,
        updateMultiple,
        createUpdater,
        updateNumber,
        updateInt,
    }
}
