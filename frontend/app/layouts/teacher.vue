<script setup lang="ts">
    if (import.meta.client) {
        const script = document.createElement('script')
        script.src = 'https://cdn.tailwindcss.com?plugins=forms,container-queries'

        script.onload = () => {
            (window as any).tailwind = (window as any).tailwind || {};
            (window as any).tailwind.config = {
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
                        },
                        fontFamily: {
                            "display": ["Space Grotesk", "sans-serif"]
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
        }

        document.head.appendChild(script)
        document.documentElement.classList.add('dark')
    }
</script>

<style>
    body {
        font-family: 'Space Grotesk', sans-serif !important;
    }

    .input-field {
        background-color: #182f34 !important;
        border: 1px solid #224249 !important;
        color: #ffffff !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 1rem !important;
        outline: none !important;
        transition: all 0.2s ease !important;
    }

    .input-field::placeholder {
        color: rgba(144, 193, 203, 0.5) !important;
    }

    .input-field:focus {
        border-color: transparent !important;
        box-shadow: 0 0 0 2px #0dccf2 !important;
    }

    .nautical-input {
        width: 100% !important;
        background-color: #224249 !important;
        border: 1px solid #2c535c !important;
        color: #ffffff !important;
        border-radius: 0.5rem !important;
        padding: 0.75rem 1rem !important;
        outline: none !important;
        transition: all 0.2s ease !important;
    }

    .nautical-input:focus {
        border-color: #FFD700 !important;
        box-shadow: 0 0 0 2px #FFD700 !important;
    }

    .nautical-label {
        display: block !important;
        font-size: 0.875rem !important;
        font-weight: 700 !important;
        font-family: "Space Grotesk", sans-serif !important;
        color: #FFD700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.15em !important;
        margin-bottom: 0.5rem !important;
    }

    .compass-bg {
        background-image: radial-gradient(
            circle at center,
            rgba(255, 215, 0, 0.05) 0%,
            transparent 70%
        ) !important;
    }


    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 !important;
    }

    .stat-card-gold {
        background: linear-gradient(135deg, #1a2e33 0%, #2c250a 100%) !important;
        border-bottom: 3px solid #FFD700 !important;
    }

    .stat-card-blue {
        background: linear-gradient(135deg, #1a2e33 0%, #0a1b2c 100%) !important;
        border-bottom: 3px solid #1E3A8A !important;
    }

    .data-table-container {
        background: rgba(26, 46, 51, 0.6) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 215, 0, 0.1) !important;
    }

    tr {
        transition: all 0.2s ease-in-out !important;
    }

    .gold-border-glow {
        box-shadow: 0 0 15px rgba(255, 215, 0, 0.1) !important;
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px !important;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #101f22 !important;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #224249 !important;
        border-radius: 10px !important;
    }
    .bounty-texture {
        background-color: #f4e4bc !important;
        background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuAvDObyIk8oLuU4DBq07iAtABXrec6jVnf5h9kNwBVUFflGZkxjbiGqUMVEYnpgY2ZXB625PJrIT4XhvL1CQSoGpHQlxbnT83LZ6pkxlrVQfmSweFp0dvPTpgcZ7tSML7v2obUjXHaAfjHxGAnRFBrTxyN90N-ubevTi10Cwek7uvsJ-kWg6Ml8TdBUcN5VG_LCd1J-NgwtfJ5LlKcw2rEziXNzQ7z-DZ4MoqNBxKeUlxETlkB1qNJ9-vdpApSEgOsG9dXWTjQYoaA) !important;
        box-shadow: inset 0 0 100px rgba(139, 69, 19, 0.2), 0 10px 30px rgba(0, 0, 0, 0.5) !important;
        background-size: cover !important;
        background-position: center !important;
    }
    .burned-edge {
        position: relative !important;
        box-shadow: inset 0 0 20px rgba(0,0,0,0.2) !important;
    }
    .timeline-line::before {
        content: "" !important;
        position: absolute !important;
        left: 19px !important;
        top: 0 !important;
        bottom: 0 !important;
        width: 2px !important;
        background: linear-gradient(to bottom, #0dccf2 0%, #224249 50%, transparent 100%) !important;
    }
    .nautical-border {
        border: 8px solid transparent !important;
        border-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 0H40V40H0V0ZM2 2V38H38V2H2Z' fill='%23224249'/%3E%3Ccircle cx='4' cy='4' r='2' fill='%23FFD700'/%3E%3Ccircle cx='36' cy='4' r='2' fill='%23FFD700'/%3E%3Ccircle cx='4' cy='36' r='2' fill='%23FFD700'/%3E%3Ccircle cx='36' cy='36' r='2' fill='%23FFD700'/%3E%3C/svg%3E") 10 stretch !important;
    }
    .nautical-texture {
        background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuBGE0bPltPSk5epGDY1P_nupAz3g-svkMpR7gmu4iRQtZXKVx3vHvnSl55PWy_Nh3tijQw0kZigeBN2i6MEpm1yhue0fPrxocBIqUrls-oH6vHfuunQ6nU_pPbyU91c95cyKgooX0F6akOtEVpsmrXNADMVMRvRqvs40z_lV9D5wLgF4EMw9JebUAg2NAEA2iILBKtrrd4b5vKtGBb6knIj4mK2FNxRrGGahVdxU8h3Ej42GU_fQIZiII9OVdGrLeRsHv_rN5UooyM) !important;
    }
    .gold-glow {
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.15) !important;
    }
    .circular-progress {
        background: conic-gradient(var(--tw-color-primary) calc(var(--progress) * 1%), #1E3A5F 0) !important;
    }
    .bounty-card {
        background: #f4e4bc !important;
        border: 12px solid #3d2b1f !important;
        color: #3d2b1f !important;
        position: relative !important;
    }
    .bounty-card::before {
        content: '' !important;
        position: absolute !important;
        inset: 0 !important;
        background: url('https://www.transparenttextures.com/patterns/paper-fibers.png') !important;
        opacity: 0.2 !important;
        pointer-events: none !important;
    }
    .nautical-grid {
        background-image: 
            radial-gradient(circle at 2px 2px, rgba(13, 204, 242, 0.05) 1px, transparent 0) !important;
        background-size: 24px 24px !important;
    }
    .gold-glow {
        box-shadow: 0 0 20px rgba(250, 204, 21, 0.05) !important;
    }
    .sidebar-gradient {
        background: linear-gradient(180deg, #0d1f23 0%, #071114 100%) !important;
    }
    .wave-bg {
        background: linear-gradient(180deg, rgba(16, 31, 34, 0.9) 0%, rgba(16, 31, 34, 1) 100%),
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='20' viewBox='0 0 100 20'%3E%3Cpath d='M0 10 Q25 0 50 10 T100 10' fill='none' stroke='%230dccf2' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E") !important;
        background-repeat: repeat !important;
    }

    .parchment-effect {
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02)) !important;
    }

    .adventure-title {
        font-family: "Almendra", serif !important;
    }

    .parchment {
        background-color: #f4ecd8 !important;
        color: #2c2416 !important;
        box-shadow: inset 0 0 100px rgba(0, 0, 0, 0.1), 0 10px 30px rgba(0, 0, 0, 0.3) !important;
    }
    .parchment-dark {
        background-color: #1a2a2d !important;
        background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuCpPnw2jsu_TCqONWJ8jetDyrIiYT9ZfXt8XeStzkXBgBhiBqHpe9ziaeZ3Vl3OFKmCVX4zfUZt9vcIe8Bm2QdI3RUeTVtBglHEbSEsm1hGY0Zkc_m8MXymI4TVm3n2Q-_iFPhX6HxpKHfQ9CDNJzbvHWgqUgUzjTuvu67MzhETuZ3rEwqo-ss9sjCDQDfTjUVu9hISpRRMpI43XGM5V9aCMIAeSrA_W5QzEtghKg0SGUTko1OHTsQcawY2Rwosegfq7oxQe_UlVkw) !important;
        border: 1px solid rgba(13, 204, 242, 0.1) !important;
    }
    .hero-overlay {
        background: linear-gradient(to top, #101f22 0%, rgba(16, 31, 34, 0.4) 50%, rgba(16, 31, 34, 0) 100%) !important;
    }

    .custom-radio:checked + label {
        border-color: currentColor;
        background-color: rgba(255, 255, 255, 0.1);
    }
</style>

<template>

    <Head>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
    </Head>

    <div class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white min-h-screen flex">
        <TeacherSidebar />
        <slot />
    </div>
</template>