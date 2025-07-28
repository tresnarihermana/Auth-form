<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Swal from 'sweetalert2';
import { defineProps } from 'vue';
import { usePage } from '@inertiajs/vue3';
import DeleteUser from '@/components/DeleteUser.vue';
const page = usePage();
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Users',
        href: '/users',
    },
];
defineProps({
    users: Array
});
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
  }})
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
        router.delete(route('users.destroy',id))
        Swal.fire({
            title: 'Deleted!',
            text: 'Your file has been deleted.',
            icon: 'success',
            timer: 1000,
            timerProgressBar: true,
        })
        })
    }

</script>

<template>
    <Head title="Users" />
    <AppLayout :breadcrumbs="breadcrumbs">
<!-- component -->
<div class="text-gray-900 bg-gray-200">
    <div class="p-4 flex">
        <h1 class="text-3xl">
            Users
        </h1>
    </div>
    <div class="px-4">
    <Button label="Add User" as="a" :href="route('users.create')"  icon="pi pi-plus" icon-pos="left" />  
</div>
    <div class="px-3 py-4 flex justify-center">
        
        <table class="w-full text-md bg-white shadow-md rounded mb-4">
            <tbody>
                <tr class="border-b">
                    <th class="text-left p-3 px-5">Name</th>
                    <th class="text-left p-3 px-5">Username</th>
                    <th class="text-left p-3 px-5">Email</th>
                    <th class="text-left p-3 px-5">Role</th>
                    <th></th>
                </tr>
                <tr  v-for="user in users" class="border-b hover:bg-orange-100 bg-gray-100">
                    <td class="p-3 px-5"><input type="text" v-model="user.name"class="bg-transparent"></td>
                    <td class="p-3 px-5"><input type="text" v-model="user.username"class="bg-transparent"></td>
                    <td class="p-3 px-5"><input type="text" v-model="user.email" class="bg-transparent"></td>
                    <td class="p-3 px-5"><input type="text" v-model="user.email" class="bg-transparent"></td>
                    <td class="p-3 px-5 flex justify-end">
                        <Link :href="route('users.edit',user.id)" type="button" class="mr-3 text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">Edit</Link>
                        <button 
                        @click="deleteUser(user.id)"
                        type="button" class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded focus:outline-none focus:shadow-outline">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

    </AppLayout>
</template>
