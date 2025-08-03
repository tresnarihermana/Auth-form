<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3';
import { can } from '@/lib/can';
import { ref, watch } from 'vue';
import { RollerCoaster } from 'lucide-vue-next';
const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Permissions',
        href: '/permissions',
    },
];
const props = defineProps<{
    permissions: {
        data: any[],
        current_page: number,
        last_page: number,
        per_page: number,
        total: number,
        next_page_url: string | null,
        prev_page_url: string | null
    },
    roles : any[],
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
        html: "permission Succesfully added",
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

function deletePermission(id) {
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
            router.delete(route('permissions.destroy', id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'This permission has been deleted.',
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Failed!',
                        text: 'Something went wrong. The permission was not deleted.',
                        icon: 'error',
                    });
                }
            });
        }
    });
}
// pagination atau apalah
const perPage = ref(new URLSearchParams(window.location.search).get('per_page') || 10)
watch(perPage, (value) => {
    router.get(route('permissions.index'), { per_page: value, page: 1 }, { preserveState: true, replace: true })
})
const form = useForm({
    search: props.filters.search || '',
    permission: props.filters.permission || null,
    role: props.filters.role || null,


})
watch(() => form.search, () => {
    form.get(route('permissions.index'), {
        preserveScroll: true,
        preserveState: true,
    });
});
watch(() => form.role, () => {
    form.get(route('permissions.index'), { preserveState: true, replace: true })
})
</script>

<template>

    <Head title="Permissions" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- component -->
        <div class="text-gray-900 bg-gray-200">
            <!-- <div class="p-4 flex">
                <h1 class="text-3xl">
                    Roles
                </h1>
            </div>
            <div class="px-4">
                <Button v-if="can('roles.create')" label="Add Role" as="a" :href="route('roles.create')"
                    icon="pi pi-plus" icon-pos="left" />
            </div> -->

            <div class="antialiased font-sans bg-gray-200">
                <div class="container mx-auto px-4 sm:px-8">
                    <div class="py-8">
                        <div>
                            <h2 class="text-2xl font-semibold leading-tight">Permissions</h2>
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
                                        <option v-for="role in roles" :key="role.id"
                                            :value="role.name">{{ role.name }}</option>
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
                                <input v-model="form.search" placeholder="Search"
                                    class="appearance-none rounded-r rounded-l sm:rounded-l-none border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                            </div>
                            <div class="relative mx-2">
                                <button v-if="can('permissions.create')" @click="router.get(route('permissions.create'))" type="button"
                                    class=" h-full rounded border block appearance-none w-full bg-green-400 border-green-400 text-white py-2 px-4 pr-8 leading-tight focus:outline-none focus:bg-green focus:border-green-500">
                                    + Add Permission
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
                                                ID
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Permission
                                            </th>
                                            <th
                                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="permission in permissions.data">
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 w-10 h-10">
                                                        {{ permission.id }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <p class="text-gray-900 whitespace-no-wrap">
                                                    {{ permission.name }}
                                                </p>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <span v-for="role in permission.roles" :key="role.id"
                                                    class="mr-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5">
                                                    {{ role.name }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                <Link v-if="can('permissions.edit')" :href="route('permissions.edit', permission.id)"
                                                    type="button"
                                                    class="mr-3 text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">
                                                Edit</Link>
                                                <button v-if="can('permissions.delete')" @click="deletePermission(permission.id)"
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
                                        {{ (permissions.current_page - 1) * permissions.per_page + 1 }}
                                        to
                                        {{
                                            permissions.current_page * permissions.per_page > permissions.total
                                                ? permissions.total
                                                : permissions.current_page * permissions.per_page
                                        }}
                                        of {{ permissions.total }} entries
                                    </span>
                                    <div class="inline-flex mt-2 xs:mt-0">
                                        <button @click="router.get(permissions.prev_page_url)"
                                            :disabled="!permissions.prev_page_url"
                                            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-l">
                                            Prev
                                        </button>

                                        <button @click="router.get(permissions.next_page_url)"
                                            :disabled="!permissions.next_page_url"
                                            class="text-sm bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-r">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </AppLayout>
</template>
