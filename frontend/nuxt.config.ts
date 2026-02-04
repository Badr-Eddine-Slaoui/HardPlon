// https://nuxt.com/docs/api/configuration/nuxt-config
// nuxt.config.ts

export default defineNuxtConfig({
  vite: {
    esbuild: false,
  },
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    'pinia-plugin-persistedstate/nuxt'
  ]
})
