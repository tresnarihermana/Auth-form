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
import { Transition } from 'vue';
import FileUpload from 'primevue/fileupload';
import { ref } from 'vue';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { computed } from 'vue';
import { useInitials } from '@/composables/useInitials';

const props = defineProps<Props>();

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
    user: User;
}
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
    avatar: null,
});

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

const photoPreview = ref(null);

const selectNewPhoto = (event) => {
    form.avatar = event.target.files[0];
    if (form.avatar) {
        photoPreview.value = URL.createObjectURL(form.avatar);
    }
};

const updateProfilePhoto = () => {
    form.post(route('profile.avatar'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('avatar');
            photoPreview.value = null;
        },
    });
}

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
                        <div class="mt-6">
            <h2 class="text-lg font-medium text-gray-900">Foto Profil</h2>
            <p class="mt-1 text-sm text-gray-600">
                Unggah atau perbarui foto profil Anda.
            </p>
        </div>
            <div class="mt-4">
                <img
                    v-if="user.avatar || photoPreview"
                    :src="photoPreview || '/storage/' + user.avatar"
                    alt="Foto Profil"
                    class="w-32 h-32 rounded-full object-cover"
                />
                <p v-else class="text-gray-500">Belum ada foto profil.</p>
            </div>

                <form @submit.prevent="updateProfilePhoto" class="mt-4 space-y-4">
                    <div>
                        <Input for="avatar" value="Pilih Foto" />
                        <input id="avatar" type="file" accept="image/*" class="mt-1 block w-full"
                            @change="selectNewPhoto" />
                        <InputError class="mt-2" :message="form.errors.avatar" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing || !form.avatar">
                            Unggah
                        </Button>

                        <p v-if="props.status === 'profile-photo-updated'" class="text-sm font-medium text-green-600">
                            Foto profil berhasil diperbarui.
                        </p>
                    </div>
                </form>
                <form @submit.prevent="submit" class="space-y-6">

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
