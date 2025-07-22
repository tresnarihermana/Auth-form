<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem, type User } from '@/types';
import InputText from 'primevue/inputtext';
import AuthBase from '@/layouts/AuthLayout.vue';
import Password from 'primevue/password';
import Swal from 'sweetalert2'


// swall


interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    user: User;
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
    email: user.email,
    avatar: user.avatar,
});
const flash = page.props?.flash?.message;
if (flash) {
    Swal.fire({
        title: 'Complete your Profile',
        text: flash,
        icon: 'info',
        confirmButtonText: 'baik'
    })
}

const submit = () => {
    form.patch(route('profile.complete'), {
        preserveScroll: true,
    });
};
</script>


<template>
    <AuthBase title="Complete Your Profile" description="Sebelum lanjut mohon isi dulu data diri anda">

        <Head title="Profile settings" />

        <div class="flex flex-col space-y-6">
            <HeadingSmall title="Profile information" description="Update your profile information" />

            <form @submit.prevent="submit" class="space-y-6 flex flex-col">
                <div class="grid gap-2">
                    <Label for="name">fullname</Label>
                    <InputText id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name"
                        placeholder="enter your fullname" />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <InputText id="username" class="mt-1 block w-full" v-model="form.username" required
                        autocomplete="username" placeholder="enter your username" />
                    <InputError class="mt-2" :message="form.errors.username" />
                </div>
                <div class="grid gap-2 w-full">
                    <Label for="password">Password</Label>
                    <Password id="password" type="password" v-model="form.password" placeholder="Password" :tabindex="3"
                        autocomplete="new-password" toggleMask inputClass="w-full" class="w-full" />
                    <InputError :message="form.errors.password" />
                </div>
                <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm password</Label>
                    <Password id="password_confirmation" type="password" required :tabindex="4"
                        autocomplete="new-password" v-model="form.password_confirmation" placeholder="Confirm password"
                        inputClass="w-full" class="w-full" toggleMask :feedback="false" />
                    <InputError :message="form.errors.password_confirmation" />
                </div>



                <div class="grid gap-2">
                    <Label for="email">Email address</Label>
                    <InputText id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                        autocomplete="username" placeholder="Email address" disabled />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div v-if="mustVerifyEmail && !user.email_verified_at">
                    <p class="-mt-4 text-sm text-muted-foreground">
                        Your email address is unverified.
                        <Link :href="route('verification.send')" method="post" as="button"
                            class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500">
                        Click here to resend the verification email.
                        </Link>
                    </p>

                    <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">Save</Button>

                    <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Saved.</p>
                    </Transition>
                </div>
            </form>
        </div>
    </Authbase>
</template>
