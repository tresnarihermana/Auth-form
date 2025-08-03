<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import Password from 'primevue/password';
import InputText from 'primevue/inputtext';
import { reactive, watch, ref, onMounted } from 'vue';
import Divider from 'primevue/divider';
import Swal from 'sweetalert2';
const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
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


  form.post(route('register'), {
    onError: (errors) => {
      console.log(errors);
    },
    onFinish: () => {
      grecaptcha.reset(); // reset captcha biar bisa dicentang lagi
    },
  });
};

const usernameWarning = ref('');
watch(() => form.username, (val) => {
    if (/\s/.test(val)) {
        usernameWarning.value = 'Username field cannot use spaces'
    } else {
        usernameWarning.value = ''
    }
})
// realtime validate password
const rules = reactive({
    minLength: false,
    hasUppercase: false,
    hasLowercase: false,
    hasNumber: false,
    hasSymbol: false,
    hasSpace: false
})
function validatePassword() {
    const val = form.password

    rules.minLength = val.length >= 8
    rules.hasUppercase = /[A-Z]/.test(val)
    rules.hasLowercase = /[a-z]/.test(val)
    rules.hasNumber = /[0-9]/.test(val)
    rules.hasSymbol = /[^A-Za-z0-9]/.test(val)
    rules.hasSpace = /\s/.test(val)
}
watch(() => form.password, validatePassword)
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
</script>

<template>
    <AuthBase title="Create an account" description="Enter your details below to create your account">

        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="name">Fullname</Label>
                    <InputText id="name" type="text" required autofocus :tabindex="1" autocomplete="fullname"
                        v-model="form.name" placeholder="Enter your fullname" />
                    <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <InputText id="username" type="text" required autofocus :tabindex="1" autocomplete="username"
                        v-model="form.username" placeholder="Enter your username" />
                    <InputError :message="form.errors.username ?? usernameWarning" />

                </div>

                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <InputText id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email"
                        placeholder="email@example.com" />
                    <InputError :message="form.errors.email" />
                </div>

                <div class="grid gap-2 w-full">
                    <Label for="password">Password</Label>
                    <Password id="password" type="password" v-model="form.password" placeholder="Password" :tabindex="3"
                        autocomplete="new-password" toggleMask inputClass="w-full" class="w-full">
                        <template #footer>
                            <Divider />
                            <ul class="pl-2 my-0 leading-normal text-sm">
                                <li v-if="!rules.minLength">Password minimal 8 karakter</li>
                                <li v-if="!rules.hasUppercase">Harus ada huruf besar</li>
                                <li v-if="!rules.hasLowercase">Harus ada huruf kecil</li>
                                <li v-if="!rules.hasNumber">Harus ada angka</li>
                                <li v-if="!rules.hasSymbol">Harus ada simbol (!@#$%^&*)</li>
                                <li v-if="rules.hasSpace">Password Tidak Boleh Menggunakan Spasi</li>
                            </ul>
                        </template>
                    </Password>

                    <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Password id="password_confirmation" type="password" required :tabindex="4"
                        autocomplete="new-password" v-model="form.password_confirmation" placeholder="Confirm password"
                        inputClass="w-full" class="w-full" toggleMask :feedback="false" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>
                
                <!-- Box reCAPTCHA v2 -->
                <div class="g-recaptcha" :data-sitekey="sitekey"></div>
                <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing">
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Create account
                </Button>
            </div>

            <div class="text-center text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
            </div>
        </form>
    </AuthBase>
</template>
