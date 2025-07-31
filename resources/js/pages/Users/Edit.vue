<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { User, type BreadcrumbItem } from '@/types';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import { Form } from '@primevue/forms';
import InputError from '@/components/InputError.vue';
import Divider from 'primevue/divider';
import { reactive, watch, ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import InputText from 'primevue/inputtext'
import Password from 'primevue/password';
import { Save } from 'lucide-vue-next';
import Checkbox from 'primevue/checkbox';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'User Edit',
        href: '/users',
    },
];
const page = usePage();
const props = defineProps({
    user: Object,
    roles: Array,
    userRoles: Array,
})
const form = useForm({
    name: props.user.name,
    username: props.user.username,
    email: props.user.email,
    password: '',
    password_confirmation: '',
    verified_email: Boolean(props.user.verified_email),
    roles: props.userRoles || [],
})

const submit = () => {
    form.put(route('users.update', props.user.id), {
        onError: (errors) => {
            console.log(errors);
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
</script>

<template>

    <Head title="Users Edit" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <div class="p-4 flex">
                <h1 class="text-3xl">
                    Edit Existed User
                </h1>
            </div>

        </div>
        <div class="px-4 py-3">
            <Button label="Back" as="a" :href="route('users.index')" icon="pi pi-arrow-left" icon-pos="left" />
        </div>
        <!-- component -->
        <div class="my-5">
            <!-- Main container for the form, responsive to screen sizes -->
            <div
                class="container mx-auto max-w-xs sm:max-w-md md:max-w-lg lg:max-w-xl shadow-md dark:shadow-white py-4 px-6 sm:px-10 bg-white dark:bg-gray-800 border-emerald-500 rounded-md">

                <div class="my-3">
                    <!-- Form title -->
                    <h1 class="text-center text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white"> Edit Existed
                        User
                    </h1>
                    <form @submit.prevent="submit">

                        <!-- Input field for 'Name' -->
                        <div class="grid gap-2">
                            <Label for="name">Fullname</Label>
                            <InputText id="name" type="text" required autofocus :tabindex="1" autocomplete="fullname"
                                v-model="form.name" placeholder="Enter fullname" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Input field for 'Username' -->
                        <div class="grid gap-2">
                            <Label for="username">Username</Label>
                            <InputText id="username" type="text" required autofocus :tabindex="1"
                                autocomplete="username" v-model="form.username" placeholder="Enter username" />
                            <InputError :message="form.errors.username ?? usernameWarning" />

                        </div>
                        <!-- Input field for 'Email' -->
                        <div class="grid gap-2">
                            <Label for="email">Email address</Label>
                            <InputText id="email" type="email" required :tabindex="2" autocomplete="email"
                                v-model="form.email" placeholder="email@example.com" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <div class="flex items-center py-3 gap-2">
                            <Checkbox inputId="verified_email" v-model="form.verified_email" :tabindex="3"
                                :binary="true" :false-value="false" :true-value="true" />
                            <label for="verified_email">Verified This Email</label>
                        </div>

                        <div class="grid gap-2 w-full">
                            <Label for="password">Password</Label>
                            <Password id="password" type="password" v-model="form.password" placeholder="Password"
                                :tabindex="3" autocomplete="new-password" toggleMask inputClass="w-full" class="w-full">
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
                                autocomplete="new-password" v-model="form.password_confirmation"
                                placeholder="Confirm password" inputClass="w-full" class="w-full" toggleMask
                                :feedback="false" />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

           <!-- Input field for 'role' -->

                        <div class="grid gap-2">
                            <Label for="roles">Roles</Label>
                            <div class="flex flex-col gap-2">
                                <div class="flex flex-wrap gap-4">
                                    <div v-for="role in roles" :key="role.name"
                                        class="flex items-center gap-2">
                                        <Checkbox v-model="form.roles" :value="role.name"
                                            :inputId="'perm-' + role.name" />
                                        <label :for="'perm-' + role.name">{{ role.name }}</label>
                                    </div>
                                </div>
                                <Message v-if="form.errors.roles" severity="error" size="small" variant="simple">
                                    {{form.errors?.roles }}</Message>
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
