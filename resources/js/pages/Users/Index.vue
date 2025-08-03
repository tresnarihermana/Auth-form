<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Swal from 'sweetalert2';
import { defineProps, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { useInitials } from '@/composables/useInitials';
import { Form } from '@primevue/forms';
import { Search, Save, Plus } from 'lucide-vue-next';
const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];
const props = defineProps<{
    users: {
        data: any[],
        current_page: number,
        last_page: number,
        per_page: number,
        total: number,
        next_page_url: string | null,
        prev_page_url: string | null
    },
    roles: any[],
    filters: {
        data: any[],
    }
}>();


const flash = page.props?.flash?.message;
if (flash) {
    let timerInterval;
    Swal.fire({
        title: "Process Success",
        icon: "success",
        html: "User Succesfully added",
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    })
}

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
                },
                

            });
        }
    });
}
const { getInitials } = useInitials();


// pagination atau apalah
const perPage = ref(new URLSearchParams(window.location.search).get('per_page') || 10)
watch(perPage, (value) => {
    router.get(route('users.index'), { per_page: value, page: 1 }, { preserveState: true, replace: true })
})
const form = useForm({
    search: props.filters.search || '',
    role: props.filters.role || null,
})
watch(() => form.search, () => {
    form.get(route('users.index'), {
        preserveScroll: true,
        preserveState: true,
    });
});
watch(() => form.role, () => {
    form.get(route('users.index'), { preserveState: true, replace: true })
})

function toggleStatus(id) {
    router.put(route('users.toggleStatus', id), {}, {
        onSuccess: () => {

        },
        onError: (errors) => {
            console.error(errors)
        }
    })
}

</script>

<template>

    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->

        <body class="antialiased font-sans bg-gray-200">
            <div class="container mx-auto px-4 sm:px-8">
                <div class="py-8">
                    <div>
                        <h2 class="text-2xl font-semibold leading-tight">Users</h2>
                    </div>
                    <div class="my-2 flex sm:flex-row flex-col">
                        <div class="flex flex-row mb-1 sm:mb-0">
                            <div class="relative">
                                <select v-model="perPage"
                                    class="appearance-none h-full rounded-l border block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option :value="5">5</option>
                                    <option :value="10">10</option>
                                    <option :value="20">20</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="relative">
                                <select v-model="form.role"
                                    class="appearance-none h-full rounded-r border-t sm:rounded-r-none sm:border-r-0 border-r border-b block appearance-none w-full bg-white border-gray-400 text-gray-700 py-2 px-4 pr-8 leading-tight focus:outline-none focus:border-l focus:border-r focus:bg-white focus:border-gray-500">
                                    <option :value="null">All</option>
                                    <option v-for="role in roles" :value="role.name">{{ role.name }}</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path
                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="block relative">
                            <span class="h-full absolute inset-y-0 left-0 flex items-center pl-2">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                                    <path
                                        d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                                    </path>
                                </svg>
                            </span>
                            <input placeholder="Search" v-model="form.search"
                                class="appearance-none rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                        </div>
                        <div class="relative mx-2">
                            <button v-if="can('users.create')" @click="router.get(route('users.create'))" type="button"
                                class=" h-full rounded border block appearance-none w-full bg-green-400 border-green-400 text-white py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-green focus:border-green-500">
                                + Add User
                            </button>
                        </div>

                    </div>
                    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Username
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Fullname
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Email Address
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data">
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 w-10 h-10">
                                                    <img class="w-full h-full rounded-full"
                                                        :src="user.avatar ? `/storage/` + user.avatar : 'https://ui-avatars.com/api/?name=' + getInitials(user.username) + '&background=random'"
                                                        :alt="user.name + `avatar`" alt="" />
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{ user.name }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                {{ user.username }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <span v-for="role in user.roles"
                                                class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                <span aria-hidden
                                                    class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span>
                                                <span class="relative">{{ role.name }}</span>
                                            </span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap">
                                                {{ user.email }}
                                            </p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <span v-if="can('users.toggleStatus')" @click="toggleStatus(user.id)"
                                                class="cursor-pointer relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                :class="user.is_active ? 'text-green-900' : 'text-red-900'">
                                                <span aria-hidden class="absolute inset-0 opacity-50 rounded-full"
                                                    :class="user.is_active ? 'bg-green-200' : 'bg-red-200'"></span>
                                                <span class="relative">{{ user.is_active ? 'Active' : 'Inactive'
                                                }}</span>
                                            </span>
                                            <span v-else
                                                class="cursor-pointer relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"
                                                :class="user.is_active ? 'text-green-900' : 'text-red-900'">
                                                <span aria-hidden class="absolute inset-0 opacity-50 rounded-full"
                                                    :class="user.is_active ? 'bg-green-200' : 'bg-red-200'"></span>
                                                <span class="relative">{{ user.is_active ? 'Active' : 'Inactive'
                                                }}</span>
                                            </span>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                            <Link v-if="can('users.show')" :href="route('users.show', user.id)"
                                                type="button"
                                                class="mr-3 text-sm bg-green-500 hover:bg-green-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">
                                            Show</Link>
                                            <Link v-if="can('users.edit')" :href="route('users.edit', user.id)"
                                                type="button"
                                                class="mr-3 text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">
                                            Edit</Link>
                                            <button v-if="can('users.delete')" @click="deleteUser(user.id)"
                                                type="button"
                                                class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">Delete</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div
                                class="px-5 py-5 bg-white border-t flex flex-col xs:flex-row items-center xs:justify-between          ">
                                <span class="text-xs xs:text-sm text-gray-900">
                                    Showing
                                    {{ (users.current_page - 1) * users.per_page + 1 }}
                                    to
                                    {{
                                        users.current_page * users.per_page > users.total
                                            ? users.total
                                            : users.current_page * users.per_page
                                    }}
                                    of {{ users.total }} entries
                                </span>
                                <div class="inline-flex mt-2 xs:mt-0">
                                    <button @click="router.get(users.prev_page_url)" :disabled="!users.prev_page_url"
                                        class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l">
                                        Prev
                                    </button>

                                    <button @click="router.get(users.next_page_url)" :disabled="!users.next_page_url"
                                        class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>

    </AppLayout>
</template>
