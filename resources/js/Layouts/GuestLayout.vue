<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue'
import { Head, Link, useForm, defineProps } from '@inertiajs/inertia-vue3';
import { ref, watch, reactive, computed, onMounted } from 'vue';
import axios from 'axios';
</script>

<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-savoy-blue">
        <div>
            <!-- Adjust the width and height of the image -->
            <img :src="imagePath" alt="TJ Gear Marketing" class="w-72 h-20 mr-2" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <slot />
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

<style>

</style>