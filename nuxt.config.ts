// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
    srcDir: 'nuxt/',

    /**
     * Manually disable nuxt telemetry.
     * @see [Nuxt Telemetry](https://github.com/nuxt/telemetry) for more information.
     */
    telemetry: true,

    $development: {
        ssr: true,
        devtools: {
            enabled: false,
        },
    },

    $production: {
        ssr: true,
    },

    app: {
        head: {
            title: 'Gopposs App',
            meta: [
                { charset: 'utf-8' },
                { name: 'viewport', content: 'width=device-width, initial-scale=1' },
            ],
            link: [
                { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' },
            ],
        },
    },

    routeRules: {
        'auth/verify': { ssr: false }
    },

    tailwindcss: {
        cssPath: '@/assets/css/main.css',
    },

    /**
     * @see https://v3.nuxtjs.org/api/configuration/nuxt.config#modules
     */
    modules: [
        '@nuxt/ui',
        '@nuxt/image',
        '@pinia/nuxt',
        'dayjs-nuxt',
        'nuxt-security',
    ],

    ui: {
        icons: ['heroicons'],
    },

    image: {
        domains: [
            import.meta.env.API_URL || 'http://127.0.0.1:8000'
        ],
        alias: {
            api: import.meta.env.API_URL || 'http://127.0.0.1:8000'
        }
    },

    dayjs: {
        locales: ['en'],
        plugins: ['relativeTime', 'utc', 'timezone'],
        defaultLocale: 'en',
        defaultTimezone: import.meta.env.APP_TIMEZONE,
    },

    typescript: {
        strict: false,
    },

    /**
     * @see https://v3.nuxtjs.org/guide/features/runtime-config#exposing-runtime-config
     */
    runtimeConfig: {
        public: {
            webURL: import.meta.env.WEB_URL,
            apiURL: import.meta.env.API_URL,
            apiPrefix: '/api/v1',
            storageBase: import.meta.env.API_URL + '/storage/',
            providers: {

            },
        },
    },
})
