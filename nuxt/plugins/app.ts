import type { FetchOptions } from 'ofetch';
import { ofetch } from 'ofetch';

export default defineNuxtPlugin({
    name: 'app',
    enforce: 'default',
    parallel: true,
    async setup(nuxtApp) {
        const config = useRuntimeConfig()
        nuxtApp.provide('storage', (path: string): string => {
            if (!path) return ''

            return path.startsWith('http://') || path.startsWith('https://') ?
                path
                : config.public.storageBase + path
        })
    }
})
