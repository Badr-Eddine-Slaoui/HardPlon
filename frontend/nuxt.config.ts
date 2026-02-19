// https://nuxt.com/docs/api/configuration/nuxt-config
// nuxt.config.ts
import colors from 'tailwindcss/colors'

export default defineNuxtConfig({
  vite: {
    esbuild: false,
  },
  modules: [
    '@nuxtjs/tailwindcss',
    '@pinia/nuxt',
    'pinia-plugin-persistedstate/nuxt'
  ],
  css: ['~/assets/css/style.css'],
  tailwindcss: {
    config: {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
              "primary": "#0dccf2",
              "background-light": "#f5f8f8",
              "background-dark": "#101f22",
              "pirate-gold": "#FFD700",
              "deep-blue": "#1E3A8A",
              "academy-panel": "#1a2e33",
              "pirate-red": "#8B0000",
              "ocean-turquoise": "#20B2AA",
              "locker-red": "#8B0000",
              "table-row-hover": "rgba(13, 204, 242, 0.05)",
              "archive-red": "#7f1d1d",
              "admin-gold": "#ecb613",
              "teacher-turquoise": "#2dd4bf",
              "student-blue": "#3b82f6",
              "gold": "#FFD700",
              "dark-red": "#8B0000",
              "marine-crimson": "#8B0000",
              "marine-crimson-dark": "#660000",
              "pirate-gold-dark": "#DAA520",
              "parchment": "#f4e4bc",
              "parchment-dark": "#d9c59a",
              "marine-dark": "#182f34",
              "marine-border": "#224249",
              "abort-red": "#8b0000",
              "marine-red": "#8b0000",
              "marine-red-dark": "#5a0000",
              "danger": "#8b0000",
              "ocean-blue": "#04101E",
              "deck-blue": "#0B1E33",
              "border-blue": "#1E3A5F",
              "turquoise": "#2DD4BF",
              "text-gold": "#E6C200",
              "text-silver": "#CCD6F6",
              "text-muted": "#8892B0",
              "gold-accent": "#facc15",
              "ocean-deep": "#071114",
              "ocean-dark": "#0a1619",
              "card-dark": "#112428",
              "border-teal": "#1a3a41",
              "accent-red": "#e63946",
              "poor-red": "#ff6b6b",
              "average-yellow": "#ffd93d",
              "excellent-green": "#6bc167",
              "grade-excellent": "#10b981",
              "grade-average": "#d4af37",
              "grade-poor": "#ef4444",
              "deep-ocean": "#0a1619",
          },
          fontFamily: {
              "display": ["Space Grotesk", "sans-serif", "Almendra", "serif"],
              "sans": ["Space Grotesk", "sans-serif"],
          },
          borderRadius: {
              "DEFAULT": "0.25rem",
              "lg": "0.5rem",
              "xl": "0.75rem",
              "full": "9999px"
          },
        },
      },
    }
  },
  app: {
    head:{
      htmlAttrs: {
        class: 'dark',
      }
    }
  }
})
