import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import dts from 'vite-plugin-dts'
import { resolve } from 'path'

export default defineConfig({
    plugins: [
        vue(),
        dts({
            include: ['resources/js/**/*.ts', 'resources/js/**/*.vue'],
            outDir: 'dist',
            rollupTypes: false,
            insertTypesEntry: true,
            strictOutput: false,
        }),
    ],
    build: {
        lib: {
            entry: resolve(__dirname, 'resources/js/index.ts'),
            name: 'SpireMail',
            formats: ['es', 'cjs'],
            fileName: (format) => `spire-mail.${format === 'es' ? 'js' : 'cjs'}`,
        },
        cssMinify: 'lightningcss',
        rollupOptions: {
            external: ['vue', '@sabrenski/spire-ui-vue', '@hugeicons/core-free-icons', '@inertiajs/vue3'],
            output: {
                globals: {
                    vue: 'Vue',
                    '@sabrenski/spire-ui-vue': 'SpireUI',
                    '@hugeicons/core-free-icons': 'HugeiconsCoreFree',
                    '@inertiajs/vue3': 'InertiaVue3',
                },
                assetFileNames: 'spire-mail.[ext]',
            },
        },
    },
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources/js'),
        },
    },
})
