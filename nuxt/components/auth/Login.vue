<script lang="ts" setup>
const config = useRuntimeConfig();
const api = useApi();
const form = ref();

type Provider = {
  name: string;
  icon: string;
  color: string;
  loading?: boolean;
};

const state = reactive({
  email: "",
  password: "",
  remember: false,
});

const onSubmit = () => api.attempt(state)

// const { refresh: onSubmit, status: loginStatus } = useFetch<any>("login", {
//   method: "POST",
//   body: state,
//   immediate: false,
//   watch: false,
//     credentials: 'include',
//     async onResponse({ response }) {
//     if (response?.status === 422) {
//       form.value.setErrors(response._data?.errors);
//     } else if (response._data?.ok) {
//       auth.token = response._data.token;
//
//       await auth.fetchUser();
//       await router.push("/");
//     }
//   }
// });

const providers = ref<{ [key: string]: Provider }>(config.public.providers);

function loginVia(provider: string): void {
  providers.value[provider].loading = true;

  const width = 640;
  const height = 660;
  const left = window.screen.width / 2 - width / 2;
  const top = window.screen.height / 2 - height / 2;

  const popup = window.open(
    `${config.public.apiBase}${config.public.apiPrefix}/login/${provider}/redirect`,
    "Sign In",
    `toolbar=no, location=no, directories=no, status=no, menubar=no, scollbars=no, resizable=no, copyhistory=no, width=${width},height=${height},top=${top},left=${left}`
  );

  const interval = setInterval(() => {
    if (!popup || popup.closed) {
      clearInterval(interval);
      providers.value[provider].loading = false;
    }
  }, 500);
}

</script>

<template>
  <div class="space-y-4">
    <div class="flex gap-4">
      <UButton
        v-for="(provider, key) in providers"
        :key="key"
        :loading="provider.loading"
        :icon="provider.icon"
        :color="provider.color"
        :label="provider.name"
        size="lg"
        class="w-full flex items-center justify-center"
        @click="loginVia(key as string)"
      />
    </div>

    <UDivider v-if="providers && providers.length" label="OR" />

    <UForm ref="form" :state="state" @submit="onSubmit" class="space-y-4">
      <UFormGroup label="Email" name="email" required>
        <UInput
          v-model="state.email"
          placeholder="you@example.com"
          icon="i-heroicons-envelope"
          trailing
          type="email"
          autofocus
        />
      </UFormGroup>

      <UFormGroup label="Password" name="password" required>
        <UInput v-model="state.password" type="password" />
      </UFormGroup>

      <UTooltip text="for 1 month" :popper="{ placement: 'right' }">
        <UCheckbox v-model="state.remember" label="Remember me" />
      </UTooltip>

      <div class="flex items-center justify-end space-x-4">
        <NuxtLink class="text-sm" to="/auth/forgot">Forgot your password?</NuxtLink>
        <UButton type="submit" label="Login" :loading="loginStatus === 'pending'" />
      </div>
    </UForm>

    <div class="text-sm">
      Don't have an account yet?
      <NuxtLink class="text-sm" to="/auth/register">Sign up now</NuxtLink>
    </div>
  </div>
</template>
