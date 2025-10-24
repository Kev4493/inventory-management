import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import svgLoader from 'vite-svg-loader';

export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    svgLoader(),
  ],
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@use "@/styles/variables" as *;`
      }
    }
  },
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  server: {
    port: 5173, // Frontend läuft hier
    proxy: {
      '/api': {
        target: 'http://inventory-backend.ddev.site:33000', // Backend-Port
        changeOrigin: true,
      }
    },
  },
})
