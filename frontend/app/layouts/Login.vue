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
                            primary: '#0dccf2',
                            'pirate-gold': '#ffd700',
                            'background-light': '#f5f8f8',
                            'background-dark': '#0a0f14',
                        },
                        fontFamily: {
                            display: ['Space Grotesk', 'sans-serif'],
                        },
                        borderRadius: {
                            DEFAULT: '0.25rem',
                            lg: '0.5rem',
                            xl: '0.75rem',
                            full: '9999px',
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
        font-family: "Space Grotesk", sans-serif
    }

    .nautical-bg {
        background-color: #0a0f14;
        background-image: radial-gradient(circle at 2px 2px, rgba(13, 204, 242, 0.05) 1px, transparent 0), linear-gradient(rgba(10, 15, 20, 0.9), rgba(10, 15, 20, 0.9)), url(https://lh3.googleusercontent.com/aida-public/AB6AXuDHRfRFn1jTxhLbjFK2SVWl8dZHlPsvdr8aU7NuEHlY6S28jaCWbxjqOa3xYICYJ1UT_04IB2YlNYf7Edjj--xKDKRB1Mi5ZJZnHRdSdw-OwSRVyWacWXC3Hzq11pI_6EhQuZ3vUdgoxsHZ-OeSRkDsMvO1CZXkQFUBuGTeYt2lITdTsrlKmtvhZYGhvl3EFqLDqZ3smBeMDwT4xO1l_ue84nowOhINGQTSaKp1iVwpHpo7uOkyZdEgYGpC95HBkbGwB7Vbf0HeLIY);
        background-size: 40px 40px, cover, cover;
        background-blend-mode: normal, multiply, luminosity
    }
</style>

<template>

    <Head>
        <Title>Login - Set Sail | Grand Line Academy</Title>
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap"
            rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
        <link
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
            rel="stylesheet" />
    </Head>

    <div class="bg-background-light dark:bg-background-dark min-h-screen flex flex-col font-display">
        <slot />
    </div>
</template>