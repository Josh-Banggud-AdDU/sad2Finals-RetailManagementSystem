<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const hovered = ref([false, false]);
const financeDropdownHovered = ref(false);
</script>

<template>
    <div>
        <div class="flex h-screen bg-vista-blue">
            <!-- Sidebar -->
            <aside class="bg-savoy-blue w-80 min-h-screen p-4 float-left">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    <img :src="imagePath" alt="TJ Gear Marketing" class="w-fit h-fit mr-2" />
                </NavLink>
                <hr class="my-6 border-t-2 border-ghost-white" />
                <div class="flex flex-col ml-6 mt-4 items-center">
                    <ul>
                        <li class="mb-8">
                            <div>
                                <NavLink @mouseover="hovered[0] = true" @mouseout="hovered[0] = false"
                                    :class="{ 'bg-vista-blue text-vista-blue rounded-md': hovered[0] }"
                                    class="group flex items-center text-2xl font-montserrat font-bold text-ghost-white px-6 py-2 transition-all duration-300 ease-in-out"
                                    :href="route('inventories.main')" :active="route().current('inventories.main')">
                                    <div class="icon-container w-8 h-8">
                                        <font-awesome-icon :icon="['fas', 'box-open']" class="icon w-6 h-6"
                                            :style="{ color: hovered[0] ? 'var(--color-vista-blue)' : '#516AC4' }" />
                                    </div>
                                    <span class="ml-3 text-ghost-white">INVENTORY</span>
                                </NavLink>
                            </div>
                        </li>

                        <li class="mb-8">
                            <div>
                                <NavLink @mouseover="hovered[1] = true" @mouseout="hovered[1] = false"
                                    :class="{ 'bg-vista-blue text-vista-blue rounded-md': hovered[1] }"
                                    class="group flex items-center text-2xl font-montserrat font-bold text-ghost-white px-6 py-2 transition-all duration-300 ease-in-out"
                                    :href="route('sales.main')" :active="route().current('sales.main')">
                                    <div class="icon-container w-8 h-8">
                                        <font-awesome-icon :icon="['fas', 'cart-shopping']" class="icon w-6 h-6"
                                            :style="{ color: hovered[1] ? 'var(--color-vista-blue)' : '#516AC4' }" />
                                    </div>
                                    <span class="ml-3 text-ghost-white">SALES</span>
                                </NavLink>
                            </div>
                        </li>

                        <li class="mb-8">
                            <div>
                                <NavLink @mouseover="hovered[2] = true" @mouseout="hovered[2] = false"
                                    :class="{ 'bg-vista-blue text-vista-blue rounded-md': hovered[2] }"
                                    class="group flex items-center text-2xl font-montserrat font-bold text-ghost-white px-6 py-2 transition-all duration-300 ease-in-out"
                                    :href="route('finances.main')" :active="route().current('finances.main')">
                                    <div class="icon-container w-8 h-8">
                                        <font-awesome-icon :icon="['fas', 'money-bills']" class="icon w-6 h-6"
                                            :style="{ color: hovered[2] ? 'var(--color-vista-blue)' : '#516AC4' }" />
                                    </div>
                                    <span class="ml-3 text-ghost-white">FINANCES</span>
                                </NavLink>
                            </div>
                        </li>

                        <li class="mb-8">
                            <div>
                                <NavLink @mouseover="hovered[3] = true" @mouseout="hovered[3] = false"
                                    :class="{ 'bg-vista-blue text-vista-blue rounded-md': hovered[3] }"
                                    class="group flex items-center text-2xl font-montserrat font-bold text-ghost-white px-6 py-2 transition-all duration-300 ease-in-out"
                                    :href="route('job_orders.main')" :active="route().current('job_orders.main')">
                                    <div class="icon-container w-8 h-8">
                                        <font-awesome-icon :icon="['fas', 'motorcycle']" class="icon w-6 h-6"
                                            :style="{ color: hovered[3] ? 'var(--color-vista-blue)' : '#516AC4' }" />
                                    </div>
                                    <span class="ml-3 text-ghost-white">SERVICES</span>
                                </NavLink>
                            </div>
                        </li>

                        <li class="mb-8">
                            <NavLink @mouseover="hovered[4] = true" @mouseout="hovered[4] = false"
                                :class="{ 'bg-vista-blue text-vista-blue rounded-md': hovered[4] }"
                                class="group flex items-center text-2xl font-montserrat font-bold text-ghost-white px-6 py-2 transition-all duration-300 ease-in-out"
                                :href="route('employees.index')" :active="route().current('employees.index')">
                                <div class="icon-container w-8 h-8">
                                    <font-awesome-icon :icon="['fas', 'users']" class="icon w-6 h-6"
                                        :style="{ color: hovered[4] ? 'var(--color-vista-blue)' : '#516AC4' }" />
                                </div>
                                <span class="ml-3 text-ghost-white">EMPLOYEES</span>
                            </NavLink>
                        </li>
                    </ul>
                    <button @click="console.log('logging out')">
                        <Link :href="route('logout')" method="post" as="button"
                            class="px-6 py-2 text-white bg-savoy-blue border border-ghost-white border-4 font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out">
                            LOG OUT
                        </Link>
                    </button>
                </div>
            </aside>

            <div class="flex flex-col grow h-screen">
                <!-- Page Heading -->
                <div class="bg-white shadow" v-if="$slots.header">
                    <div class="max-w-7xl py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </div>

                <!-- Page Content -->
                <main class="grow flex">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            imagePath: '/images/TJ GEAR MARKETING.png', // Adjust the image path accordingly
        };
    },
};
</script>

<style scoped>
.icon-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f7f8ff;
    /* Default background color */
    border-radius: 50%;
    /* Creates a circular shape */
}

.icon {
    font-size: 1rem;
    /* Reset font size to default (adjust as needed) */
    color: #F7F8FF;
    /* Default icon color */
    transition: color 0.2s;
    /* Smooth color transition */
}

.icon-container:hover .icon {
    color: #7991E6;
    /* Change color on hover */
}

.dropdown-menu {
    max-height: 0;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-10px);
    transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out, transform 0.3s ease-in-out, pointer-events 0.3s ease-in-out;
}

.dropdown-menu.bg-white {
    background-color: white;
}

/* Add this rule to align the dropdown with the other sections */
.relative.group:hover .dropdown-menu {
    max-height: 1000px;
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}
</style>

