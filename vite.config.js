import { defineConfig } from 'vite'
import { resolve } from 'path'

export default defineConfig({
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: {
                'modules-admin': resolve(__dirname, 'assets/js/modules-admin.js'),
                'wireframe': resolve(__dirname, 'src/Modules/Wireframe/resources/assets/module-wireframe.js')
            },
            output: {
                entryFileNames: 'js/[name].js',
                chunkFileNames: 'js/[name].js',
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.endsWith('.css')) {
                        return 'css/[name][extname]'
                    }
                    return 'assets/[name][extname]'
                }
            }
        }
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "${resolve(__dirname, 'assets/css/_variables.scss')}";`
            }
        }
    }
}) 