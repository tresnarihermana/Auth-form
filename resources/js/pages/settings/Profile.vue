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
// import FileUpload from 'primevue/fileupload';
import Avatar from 'primevue/avatar';
import { ref } from 'vue';
import axios from 'axios';
import toastr from 'toastr';



interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    user: User;
}

defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: 'Profile settings',
        href: '/settings/profile',
    },
];

const page = usePage();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    username: user.username,
    email: user.email,
    avatar: user.avatar,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
// const fileInput = ref(null);
// const openFileInput = () => {
//     fileInput.value.click();
// }

// const profilePicture = ref(null);

// const handleFileChange = (event) => {
//     const file = event.target.files[0];
//     profilePicture.value = URL.createObjectURL(file);
//     const formData = new FormData();
//     formData.append('avatar', file);
//     axios.post('/profile/avatar', formData, {
//         headers: {
//             'Content-Type': 'multipart/form-data',
//         }
//     }).then(res => {
//         form.avatar = res.data.avatar;
//         toastr.success('Berhasil upload foto!');
//     }).catch(err => {
//         console.error(err);
//         toastr.error('Gagal upload foto');
//     });
// }
 </script>
 <style>
 .profile-user-img {
     cursor: pointer;
 }
</style>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">

        <Head title="Profile settings" />

        <SettingsLayout>
            <div class="flex flex-col space-y-6">
                <HeadingSmall title="Profile information" description="Update your name and email address" />

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- <div class="grid gap-2">
                        <Label for="avatar">Add Profile Photo</Label>
                        <Avatar @click="openFileInput"
                            :image="profilePicture ? profilePicture : `/storage/${user.avatar}`"
                            class="mb-2 profile-user-img" size="xlarge" shape="circle" />
                        <Input @change="handleFileChange" ref="fileInput" id="avatar" name="avatar"
                            class="mt-1 block w-full" type="file" accept="image/*" />
                        <InputError class="mt-2" :message="form.errors.avatar" />
                    </div> -->
                    <div class="grid gap-2">
                        <Label for="name">fullname</Label>
                        <InputText id="name" class="mt-1 block w-full" v-model="form.name" required
                            autocomplete="name" placeholder="enter your fullname" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="grid gap-2">  
                        <Label for="username">Username</Label>
                        <InputText id="username" class="mt-1 block w-full" v-model="form.username" required
                            autocomplete="username" placeholder="enter your username" />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="email">Email address</Label>
                        <InputText id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                            autocomplete="username" placeholder="Email address" />
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

            <DeleteUser />
        </SettingsLayout>
    </AppLayout>
</template>
