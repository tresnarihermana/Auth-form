<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm} from '@inertiajs/vue3';
import Button from 'primevue/button';
import { Form } from '@primevue/forms';
import InputText from 'primevue/inputtext'
import Password from 'primevue/password';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Create',
        href: '/users',
    },
];
const form = useForm({
    name: '',
    username: '',
    email: '',
    role: '',
})
</script>

<template>
    <Head title="Users Create" />
    <AppLayout :breadcrumbs="breadcrumbs">
<!-- component -->
<div class="text-gray-900 bg-gray-200">
    <div class="p-4 flex">
        <h1 class="text-3xl">
            Create New User
        </h1>
    </div>
    
</div>
<div class="px-4 py-3">
<Button label="Back" as="a" :href="route('users.index')"  icon="pi pi-plus" icon-pos="left" />  
</div>
<!-- component -->
    <div class="my-5">
        <!-- Main container for the form, responsive to screen sizes -->
        <div class="container mx-auto max-w-xs sm:max-w-md md:max-w-lg lg:max-w-xl shadow-md dark:shadow-white py-4 px-6 sm:px-10 bg-white dark:bg-gray-800 border-emerald-500 rounded-md">
            
            <div class="my-3">
                <!-- Form title -->
                <h1 class="text-center text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Create New User</h1>
                <form action="" method="POST">
                
                    <!-- Input field for 'Name' -->
                <div class="grid gap-2">
                    <Label for="name">Fullname</Label>
                    <InputText id="name" type="text" required autofocus :tabindex="1" autocomplete="fullname"
                        v-model="form.name" placeholder="Enter your fullname" />
                    <InputError :message="form.errors.name" />
                </div>

                    <!-- Input field for 'Username' -->
                 <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <InputText id="username" type="text" required autofocus :tabindex="1" autocomplete="username"
                        v-model="form.username" placeholder="Enter your username" />
                    <InputError :message="form.errors.username ?? usernameWarning" />

                </div>
                    <!-- Input field for 'Email' -->
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
                    
                    <!-- Input field for 'role' -->
                    <div class="my-2">
                        <label for="class" class="text-sm sm:text-md font-bold text-gray-700 dark:text-gray-300">Role</label>
                        <input type="text" name="class" class="block w-full border border-emerald-500 outline-emerald-800 px-2 py-2 text-sm sm:text-md rounded-md my-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white" id="class">
                    </div>

                    <!-- Save button -->
                    <button class="px-4 py-1 bg-emerald-500 rounded-md text-black text-sm sm:text-lg shadow-md">Save</button>
                </form>
            </div>
        </div>
    </div>
    </AppLayout>
</template>
