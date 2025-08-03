<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { reactive, watch, ref } from 'vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { type BreadcrumbItem, type User } from '@/types';
import Password from 'primevue/password';
import Swal from 'sweetalert2';

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Password settings',
        href: '/settings/password',
    },
];
const page = usePage();

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);
const user = page.props.auth.user as User;
const form = useForm({
    password: '',
    password_confirmation: '',
    current_password: '',
});

const updatePassword = () => {
    form.put(route('setting.password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            Swal.fire({
                title: "Process Success",
                text: "Password Berhasil Diperbarui",
                icon: "success"
            });
        },
        onError: (errors: any) => {
            Swal.fire({
                title: "Process Failed",
                text: form.errors.current_password ?? form.errors.password ?? form.errors.password_confirmation,
                icon: "error"

            });
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
const IsNewUser = ref(false);
const flash = page.props?.flash?.message;
if (!user.has_password || flash) {
    IsNewUser.value = true;
    Swal.fire({
        icon: "info",
        title: "Complete Your Password",
        text: "Please Complete your Password info before proceed",
    });
} else {
    IsNewUser.value = false;
}
// realtime validate password
const rules = reactive({
  minLength: false,
  hasUppercase: false,
  hasLowercase: false,
  hasNumber: false,
  hasSymbol: false,
  hasSpace: false,
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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Password settings" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall title="Update password"
                    description="Ensure your account is using a long, random password to stay secure" />

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2" v-if="!IsNewUser">
                        <Label for="current_password">Current password</Label>
                        <Password id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                            type="password" toggleMask inputClass="w-full" :feedback="false" class="mt-1 block w-full"
                            autocomplete="current-password" placeholder="Current password" />
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">New password</Label>
                        <Password id="password" ref="passwordInput" v-model="form.password" toggleMask
                            class="mt-1 block w-full" autocomplete="new-password" placeholder="New password"
                            inputClass="w-full" :class="'w-full'">
                            <template #footer>
                                <Divider />
                                <ul class="pl-2 my-0 leading-normal text-sm">
                                    <li v-if="!rules.minLength">Password minimal 8 karakter</li>
                                    <li v-if="!rules.hasUppercase">Harus ada huruf besar</li>
                                    <li v-if="!rules.hasLowercase">Harus ada huruf kecil</li>
                                    <li v-if="!rules.hasNumber">Harus ada angka</li>
                                    <li v-if="!rules.hasSymbol">Harus ada simbol (!@#$%^&*)</li>
                                    <li v-if="rules.hasSpace">Password tidak boleh menggunakan spasi</li>
                                </ul>
                            </template>
</Password>
<InputError :message="form.errors.password" />
</div>

<div class="grid gap-2">
                        <Label for="password_confirmation">Confirm password</Label>
                        <Password
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            toggleMask
                            :feedback="false"
                            class="mt-1 block w-full"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                            inputClass="w-full"
                            :class="'w-full'"
                        />
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

<div class="flex items-center gap-4">
                        <Button :disabled="form.processing">Save password</Button>

                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                        </Transition>
                    </div>
</form>
</div>
</SettingsLayout>
</AppLayout>
</template>
