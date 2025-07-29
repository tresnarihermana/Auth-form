<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Swal from 'sweetalert2';
import { usePage } from '@inertiajs/vue3';
import {can} from '@/lib/can';
const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Roles',
        href: '/roles',
    },
];
defineProps({
    roles: Array
});
const flash = page.props?.flash?.message;
if (flash) {
let timerInterval;
Swal.fire({
  title: "Process Success",
  icon: "success",
  html: "role Succesfully added",
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
  }})
}

function deleteRole(id) {
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
        router.delete(route('roles.destroy', id), {
            onSuccess: () => {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'This role has been deleted.',
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
    <Head title="roles" />
    <AppLayout :breadcrumbs="breadcrumbs">
<!-- component -->
<div class="text-gray-900 bg-gray-200">
    <div class="p-4 flex">
        <h1 class="text-3xl">
            Roles
        </h1>
    </div>
    <div class="px-4">
    <Button v-if="can('roles.create')" label="Add Role" as="a" :href="route('roles.create')"  icon="pi pi-plus" icon-pos="left" />  
</div>
    <div class="px-3 py-4 flex justify-center">
        
        <table class="w-full text-md bg-white shadow-md rounded mb-4">
            <tbody>
                <tr class="border-b">
                    <th class="text-left p-3 px-5">ID</th>
                    <th class="text-left p-3 px-5">Name</th>
                    <th class="text-left p-3 px-5">Permissions</th>
                    <th></th>
                </tr>
                <tr  v-for="role in roles" class="border-b hover:bg-orange-100 bg-gray-100">
                    <td class="p-3 px-5"><input type="text" v-model="role.id"class="bg-transparent" disabled></td>
                    <td class="p-3 px-5"><input type="text" v-model="role.name"class="bg-transparent" disabled></td>
                    <td class="p-3 px-5">
                        <span
                 
                        v-for="permission in role.permissions"
                        :key="permission.id"
                        class="mr-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5"
                        >
                        {{ permission.name }}
                        </span>


                    </td>
                    <td class="p-3 px-5 flex justify-end">
                        <Link v-if="can('roles.edit')" :href="route('roles.edit',role.id)" type="button" class="mr-3 text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">Edit</Link>
                        <button v-if="can('roles.delete')"
                        @click="deleteRole(role.id)"
                        type="button" class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    </AppLayout>
</template>
