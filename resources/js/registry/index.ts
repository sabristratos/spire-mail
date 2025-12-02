/**
 * Block Registry
 *
 * Centralized registration and lookup for block components and metadata.
 */

export {
    type BlockRegistration,
    getBlockRegistration,
    getCanvasComponent,
    getPropertiesComponent,
    getBlockIcon,
    isWrapperBlock,
    canBlockNest,
    getAllBlockTypes,
    getBlocksByCategory,
    getAvailableBlocks,
    getNestableBlocks,
} from './blockRegistry'
