import { computed, type ComputedRef } from 'vue'
import { state } from './state'
import { deepClone } from '../../utils/clone'

/**
 * Saves the current state to history for undo/redo.
 */
export function pushHistory(): void {
    const snapshot = deepClone(state.value.blocks)
    state.value.history = state.value.history.slice(0, state.value.historyIndex + 1)
    state.value.history.push(snapshot)
    state.value.historyIndex = state.value.history.length - 1
}

/**
 * Reverts to the previous state in history.
 */
export function undo(): void {
    if (state.value.historyIndex <= 0) return
    state.value.historyIndex--
    state.value.blocks = deepClone(state.value.history[state.value.historyIndex])
    state.value.selectedBlockId = null
    state.value.isDirty = true
}

/**
 * Advances to the next state in history.
 */
export function redo(): void {
    if (state.value.historyIndex >= state.value.history.length - 1) return
    state.value.historyIndex++
    state.value.blocks = deepClone(state.value.history[state.value.historyIndex])
    state.value.selectedBlockId = null
    state.value.isDirty = true
}

/**
 * Returns whether undo is available.
 */
export function canUndo(): ComputedRef<boolean> {
    return computed(() => state.value.historyIndex > 0)
}

/**
 * Returns whether redo is available.
 */
export function canRedo(): ComputedRef<boolean> {
    return computed(() => state.value.historyIndex < state.value.history.length - 1)
}
