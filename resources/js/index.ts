import '../css/index.css'

export { default as EmailEditor } from './Components/Editor/EmailEditor.vue'
export { default as EditorSidebar } from './Components/Editor/EditorSidebar.vue'
export { default as EditorCanvas } from './Components/Editor/EditorCanvas.vue'
export { default as EditorProperties } from './Components/Editor/EditorProperties.vue'
export { default as PreviewModal } from './Components/Editor/PreviewModal.vue'

export { default as CanvasBlock } from './Components/Canvas/CanvasBlock.vue'
export { default as CanvasDropZone } from './Components/Canvas/CanvasDropZone.vue'

export { default as TextBlock } from './Components/Blocks/TextBlock.vue'
export { default as ImageBlock } from './Components/Blocks/ImageBlock.vue'
export { default as ButtonBlock } from './Components/Blocks/ButtonBlock.vue'

export { default as TextProperties } from './Components/Properties/TextProperties.vue'
export { default as ImageProperties } from './Components/Properties/ImageProperties.vue'
export { default as ButtonProperties } from './Components/Properties/ButtonProperties.vue'

export { default as TemplatesIndex } from './Pages/Templates/Index.vue'
export { default as TemplatesEdit } from './Pages/Templates/Edit.vue'
export { default as TemplatesCreate } from './Pages/Templates/Create.vue'

export * from './composables'
export * from './stores/editorStore'
export * from './types'
