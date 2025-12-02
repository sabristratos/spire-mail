import { toRaw } from 'vue'

/**
 * Creates a deep clone of an object using structuredClone when available,
 * with a fallback to JSON serialization for older browsers or non-cloneable objects.
 * Handles Vue reactive proxies by unwrapping them first.
 *
 * @param obj - The object to clone
 * @returns A deep clone of the object
 *
 * @example
 * const original = { nested: { value: 1 } }
 * const cloned = deepClone(original)
 * cloned.nested.value = 2
 * // original.nested.value is still 1
 */
export function deepClone<T>(obj: T): T {
    const rawObj = toRaw(obj)

    if (typeof structuredClone === 'function') {
        try {
            return structuredClone(rawObj)
        } catch {
            return JSON.parse(JSON.stringify(rawObj))
        }
    }

    return JSON.parse(JSON.stringify(rawObj))
}
