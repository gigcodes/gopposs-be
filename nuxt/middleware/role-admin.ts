export default defineNuxtRouteMiddleware(async (to, from) => {
    const nuxtApp = useNuxtApp()
    const api = useApi()
    await api.checkUser()
    if (api.loggedIn.value
        // && !api.user.roles.includes('admin')
    ) {
        return nuxtApp.runWithContext(() => {
            useToast().add({
                icon: "i-heroicons-exclamation-circle-solid",
                title: "Access denied.",
                color: "red",
            });

            return navigateTo('/')
        })
    }
})
