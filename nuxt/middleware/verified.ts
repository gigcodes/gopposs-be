export default defineNuxtRouteMiddleware(async (to, from) => {
    const nuxtApp = useNuxtApp()
    const api = useApi()
    await api.checkUser()
    if (api.loggedIn.value
        // && auth.user.must_verify_email
    ) {
        return nuxtApp.runWithContext(() => {
            useToast().add({
                icon: "i-heroicons-exclamation-circle-solid",
                title: "Please confirm your email.",
                color: "red",
            });

            return navigateTo('/account/general')
        })
    }
})
