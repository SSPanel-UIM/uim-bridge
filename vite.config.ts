import path from 'path'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tsconfigPaths from 'vite-tsconfig-paths'

// https://vitejs.dev/config/
export default defineConfig({
  resolve: {
    alias: {
      '~/': `${path.resolve(__dirname, 'src')}/`
    }
  },
  server: {
    proxy: {
      '/prefetch/': {
        target: 'http://www.example.com',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/prefetch/, '')
      },
      '/api/': {
        target: 'http://www.example.com',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '')
      }
    }
  },
  plugins: [react(), tsconfigPaths()],
  build: {
    rollupOptions: {
      input: {
        index: path.resolve(__dirname, 'src/pages/index.html'),
        login: path.resolve(__dirname, 'src/pages/auth/login/index.html')
      }
    }
  }
})
