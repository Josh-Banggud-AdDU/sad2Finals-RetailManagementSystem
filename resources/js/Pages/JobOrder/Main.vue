<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/inertia-vue3';
</script>

<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">Services</h2>
        </template>

        <div class="max-w-xl mx-auto h-full flex flex-col space-y-10 items-center justify-center py-12">
            <Link tabIndex="1"
                class="px-8 md:px-24 py-10 text-3xl w-full md:w-108 font-bold text-savoy-blue text-center bg-ghost-white rounded-lg hover:text-ghost-white hover:bg-savoy-blue transition-all duration-300 ease-in-out"
                :href="route('job_orders.create')">
                <span class="text-uppercase">Create Job Order</span>
            </Link>
            <Link tabIndex="1"
                class="px-8 md:px-24 py-10 text-3xl w-full md:w-108 font-bold text-savoy-blue text-center bg-ghost-white rounded-lg hover:text-ghost-white hover:bg-savoy-blue transition-all duration-300 ease-in-out"
                :href="route('job_orders.index')">
            <span class="text-uppercase">View Job Order History</span>
            </Link>
            <Link tabIndex="1"
                class="px-8 md:px-24 py-10 text-3xl w-full md:w-108 font-bold text-savoy-blue text-center bg-ghost-white rounded-lg hover:text-ghost-white hover:bg-savoy-blue transition-all duration-300 ease-in-out"
                :href="route('services.index')">
            <span class="text-uppercase">View Services</span>
            </Link>
        </div>
    </BreezeAuthenticatedLayout>
</template>