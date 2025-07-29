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
import { useInitials } from '@/composables/useInitials';

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
const { getInitials } = useInitials();
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
            <!-- component -->
            <div class="flex items-center h-screen w-full justify-center">

                <div class="max-w-xs">
                    <div class="bg-white shadow-xl rounded-lg py-3">
                        <div class="photo-wrapper p-2">
                            <img class="w-32 h-32 rounded-full mx-auto"
                                :src="props.user.avatar ? `/storage/` + props.user.avatar : 'https://ui-avatars.com/api/?name=' + getInitials(props.user.username) + '&background=random'"
                                :alt="props.user.name + `avatar`"
                                alt="John Doe">
                        </div>
                        <div class="p-2">
                            <h3 class="text-center text-xl text-gray-900 font-medium leading-8">{{ props.user.username }}</h3>
                            <div class="text-center text-blue-400 text-xs font-semibold">
                                {{ props.userRoles[0] }}
                            </div>
                            <table class="text-xs my-3">
                                <tbody>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Fullname</td>
                                        <td class="px-2 py-2">{{ props.user.name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Username</td>
                                        <td class="px-2 py-2">{{ props.user.username }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Email</td>
                                        <td class="px-2 py-2">{{ props.user.email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Email Verified At</td>
                                        <td class="px-2 py-2">{{props.user.email_verified_at}}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Created at</td>
                                        <td class="px-2 py-2">{{ props.user.created_at}}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Updated at</td>
                                        <td class="px-2 py-2">{{ props.user.updated_at }}</td>
                                    </tr>
 
                                </tbody>
                            </table>

                            <div class="text-center my-3">
                                <a class="text-xs text-indigo-500 italic hover:underline hover:text-indigo-600 font-medium"
                                    href="#">View Profile</a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
