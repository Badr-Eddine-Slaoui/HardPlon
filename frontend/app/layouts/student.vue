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
                            primary: "#0dccf2",
                            "pirate-gold": "#d4af37",
                            "pirate-gold-dark": "#b8860b",
                            "background-light": "#f5f8f8",
                            "background-dark": "#101f22",
                            "accent-red": "#e63946",
                            "grade-excellent": "#10b981",
                            "grade-average": "#d4af37",
                            "grade-poor": "#ef4444",
                            "poor-red": "#ff6b6b",
                            "average-yellow": "#ffd93d",
                            "excellent-green": "#6bc167",
                            "deep-ocean": "#0a1619",
                        },
                        fontFamily: {
                            display: ["Almendra", "serif"],
                            sans: ["Space Grotesk", "sans-serif"],
                        },
                        borderRadius: {
                            DEFAULT: "0.25rem",
                            lg: "0.5rem",
                            xl: "0.75rem",
                            full: "9999px",
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
        font-family: "Space Grotesk", sans-serif !important;
    }
    .adventure-title {
        font-family: "Almendra", serif !important;
    }
    .wave-bg {
        background:
        linear-gradient(
            180deg,
            rgba(16, 31, 34, 0.95) 0%,
            rgba(16, 31, 34, 1) 100%
        ),
        url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='20' viewBox='0 0 100 20'%3E%3Cpath d='M0 10 Q25 0 50 10 T100 10' fill='none' stroke='%230dccf2' stroke-width='0.5' opacity='0.1'/%3E%3C/svg%3E") !important;
        background-repeat: repeat !important;
    }
    .parchment-effect {
        background: linear-gradient(
            to bottom,
            rgba(255, 255, 255, 0.05),
            rgba(255, 255, 255, 0.02)
        ) !important;
    }
    .bounty-scroll::-webkit-scrollbar {
        width: 4px !important;
    }
    .bounty-scroll::-webkit-scrollbar-track {
        background: rgba(16, 31, 34, 0.5) !important;
    }
    .bounty-scroll::-webkit-scrollbar-thumb {
        background: #224249 !important;
        border-radius: 10px !important;
    }
    .parchment-box {
        background-color: #f3e5ab;
        background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuDnPhRNb9m61Z2n5YSsVhevZ-gyuXBI6nmKBy0LXDSG3lpmcSV-lZh7ocK6MpcZ5Kh7sRJqH_JZ1pXAntr4qzFk8Wqy6EMSi1G0gKzdXfO5P3afB0NDrNkPI1W7MILl-DbFyS2EN9k7RRYGjWk3sueaMaOHuvMYfy3U4XkRrfM92dWRf9iUU8FfoQ0PsNjoItKt0ZhCOpqDJZ8sKIgmJ82GonTw_RvkWA77AgoazBs1mZShn1HKLCNHPy5ws87lrIs7qreBuJEzSRU);
        color: #4a3728;
        border: 2px solid #c5b358;
        box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.1), 0 5px 15px rgba(0, 0, 0, 0.2)
    }
    .split-pane-height {
        height: calc(100vh - 4rem - 2.5rem)
    }
    .parchment-static {
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, rgba(16, 31, 34, 0.2) 100%);
        border-left: 4px solid #d4af37;
    }
    .bounty-card {
        background: #fdf5e6;
        color: #4a3728;
        border: 8px solid #3d2b1f;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .modal-overlay {
        background-color: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(4px);
    }
</style>

<template>

    <Head>
        <link
            href="https://fonts.googleapis.com/css2?family=Almendra:wght@400;700&amp;family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
    </Head>

    <div class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-white min-h-screen flex">
        <StudentSidebar />
        <slot />
    </div>
</template>