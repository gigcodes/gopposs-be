export default defineNuxtRouteMiddleware(async (to, from) => {
    const nuxtApp = useNuxtApp()
    const api = useApi()
    await api.checkUser()
    if (api.loggedIn.value) {
        return nuxtApp.runWithContext(() => navigateTo('/'))
    }
})
