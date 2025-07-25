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
import { watch, ref } from 'vue';
import { User } from '../../types/index';

const form = useForm({
    name: '',
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
const usernameWarning = ref('');
watch(() => form.username, (val) => {
    if(/\s/.test(val)){
        usernameWarning.value = 'Username field cannot use spaces'
    } else{
        usernameWarning.value = ''
    }
})
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
                    <InputError :message="form.errors.username" />
                    <p v-if="usernameWarning" class="text-sm text-red-600 mt-1">
                        {{ usernameWarning }}
                    </p>

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
