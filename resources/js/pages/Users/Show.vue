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
import { TrashIcon, PencilIcon } from 'lucide-vue-next';
import Checkbox from 'primevue/checkbox';
import { useInitials } from '@/composables/useInitials';
import { can } from '@/lib/can';
import { router } from '@inertiajs/vue3';
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
function deleteUser(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('users.destroy', id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This user has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The role was not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
}
</script>



<template>

    <Head title="User Info" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <div class="p-4 flex">
                <h1 class="text-3xl">
                    User Info
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

                <div class="max-w-lg">
                    <div class="bg-white shadow-xl rounded-lg py-3">
                        <div class="photo-wrapper p-2">
                    <img class="w-full h-56 object-cover object-center"
                        :src="props.user.avatar ? `/storage/` + props.user.avatar : 'https://ui-avatars.com/api/?name=' + getInitials(props.user.username) + '&background=random'"
                        :alt="props.user.name + `avatar`" alt="John Doe">
          
                        </div>
                        <div class="p-2">
                            <h3 class="text-center text-xl text-gray-900 font-medium leading-8">{{ props.user.name }}
                            </h3>
                            <div class="text-center text-blue-400 text-xs font-semibold">
                                {{ props.user.username }}
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
                                        <td class="px-2 py-2" v-if="props.user.email_verified_at">
                                            {{ props.user.email_verified_at }}</td>
                                        <td class="px-2 py-2 text-red-500" v-else>{{ 'Not Verified' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Created at</td>
                                        <td class="px-2 py-2">{{ props.user.created_at }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Updated at</td>
                                        <td class="px-2 py-2">{{ props.user.updated_at }}</td>
                                    </tr>
                                    <tr>
                                        <td class="px-2 py-2 text-gray-500 font-semibold">Role</td>
                                        <td class="px-2 py-2 text-blue-500 font-semibold">{{ String(props.userRoles) }}
                                        </td>
                                    </tr>

                                </tbody>
                            </table>

                            <div class="text-center my-3">
                                <table >
                                    <tbody>
                                        <tr>
                                            <td class="px-2 py-2">
                                                <Button  v-if="can('users.edit')" as="a" :href="route('users.edit', props.user.id)" size="small" severity="info">
                                                    <PencilIcon />
                                                    Edit
                                                </Button>
                                            </td>
                                            <td class="px-2 py-2">
                                                <Button v-if="can('users.delete')" @click="deleteUser(props.user.id)" size="small" severity="danger">
                                                    <TrashIcon />
                                                    Delete
                                                </Button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
