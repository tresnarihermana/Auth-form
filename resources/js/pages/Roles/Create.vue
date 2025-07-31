<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputError from '@/components/InputError.vue';
import InputText from 'primevue/inputtext'
import Checkbox from 'primevue/checkbox';
import CheckboxGroup from 'primevue/checkboxgroup';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Role Create',
        href: '/roles',
    },
];
defineProps({
    "permissions": Array
});
const form = useForm({
    name: '',
    permissions: [],
})

const submit = () => {
    form.post(route('roles.store'), {
        onError: (errors) => {
            console.log(errors);
        },
    });
};
</script>

<template>

    <Head title="roles Create" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <div class="p-4 flex">
                <h1 class="text-3xl">
                    Create New Role
                </h1>
            </div>

        </div>
        <div class="px-4 py-3">
            <Button label="Back" as="a" :href="route('roles.index')" icon="pi pi-arrow-left" icon-pos="left" />
        </div>
        <!-- component -->
        <div class="my-5">
            <!-- Main container for the form, responsive to screen sizes -->
            <div
                class="container mx-auto max-w-xs sm:max-w-md md:max-w-lg lg:max-w-xl shadow-md dark:shadow-white py-4 px-6 sm:px-10 bg-white dark:bg-gray-800 border-emerald-500 rounded-md">

                <div class="my-3">
                    <!-- Form title -->
                    <h1 class="text-center text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Create New Role
                    </h1>
                    <form @submit.prevent="submit">

                        <!-- Input field for 'Name' -->
                        <div class="grid gap-2">
                            <Label for="name">Role name</Label>
                            <InputText id="name" type="text" required autofocus :tabindex="1" autocomplete="role_name"
                                v-model="form.name" placeholder="Enter Role Name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="permission">Permission</Label>
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap gap-4">
                                    <div v-for="permission in permissions" :key="permission.id"
                                        class="flex items-center gap-2">
                                        <Checkbox v-model="form.permissions" :value="permission.id"
                                            :inputId="'perm-' + permission.id" />
                                        <label :for="'perm-' + permission.id">{{ permission.name }}</label>
                                    </div>
                                </div>
                                <Message v-if="form.errors.permissions" severity="error" size="small" variant="simple">{{
                                    form.errors?.permissions }}</Message>
                            </div>
                        </div>
                        <!-- Save button -->
                        <div class="py-2 ">
                            <Button label="Save" icon="pi pi-save" iconPos="right" @click="submit" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
