<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import InputText from 'primevue/inputtext';
import { defineProps, onMounted } from 'vue';
import Swal from 'sweetalert2';

defineProps<{
    status?: string;
}>();

const form = useForm({
    email: '',
    'g-recaptcha-response': '',
});

const submit = () => {
     const token = grecaptcha.getResponse();
  if (!token) {
    Swal.fire({
      icon: 'warning',
      title: 'Oops!',
      text: 'Harap centang reCAPTCHA terlebih dahulu!',
    });
    return;
  }
  form['g-recaptcha-response'] = token;
    form.post(route('password.email'));
};
// buat recaptcha
onMounted(() => {
    const recaptchaScriptId = 'recaptcha-script';

    if (!document.getElementById(recaptchaScriptId)) {
        const script = document.createElement('script');
        script.id = recaptchaScriptId;
        script.src = 'https://www.google.com/recaptcha/api.js';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    }
});
const sitekey = import.meta.env.VITE_RECAPTCHA_SITE_KEY;
// akhir recapcay
</script>

<template>
    <AuthLayout title="Forgot password" description="Enter your email to receive a password reset link">
        <Head title="Forgot password" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <div class="space-y-6">
            <form @submit.prevent="submit">
                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <InputText id="email" type="email" name="email" autocomplete="off" v-model="form.email" autofocus placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>
                <div class="g-recaptcha" :data-sitekey="sitekey"></div>

                <div class="my-6 flex items-center justify-start">
                    <Button class="w-full" :disabled="form.processing">
                        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                        Email password reset link
                    </Button>
                </div>
            </form>

            <div class="space-x-1 text-center text-sm text-muted-foreground">
                <span>Or, return to</span>
                <TextLink :href="route('login')">log in</TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
