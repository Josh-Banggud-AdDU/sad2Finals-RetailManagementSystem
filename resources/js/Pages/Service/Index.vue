<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import AddServicesModal from '@/Components/AddServicesModal.vue'
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const props = defineProps({
    services: Array,
})

const selectedService = ref({
    serviceName: null,
    serviceID: null,
})

const formatServiceID = (service) => {
    return String(service).padStart(5, '0');
};

const form = useForm();
function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('employees.destroy', id));
    }
}
const searchEmployee = ref("");
const employeeSuggestions = ref([]);
const searchEmployees = async () => {
    if (searchEmployee.value != "" && runEmployeeSearch.value == true) {
        const response = await fetch(`/search_employees?searchEmployee=${searchEmployee.value}`);
        const data = await response.json();

        const uniqueEmployeeNames = new Set();
        employeeSuggestions.value = data.filter((suggestion) => {
            if (!uniqueEmployeeNames.has(suggestion.employeeName)) {
                uniqueEmployeeNames.add(suggestion.employeeName);
                return true;
            }
            return false;
        });

        if (employeeSuggestions.value) {
            employeeSuggestions.value = await employeeSuggestions.value.slice(0, 6);
        }
    }
}
const selectEmployeeSuggestion = (employeeSuggestion) => {
    runEmployeeSearch.value = false;
    searchEmployee.value = employeeSuggestion.employeeName;
    clearEmployeeSuggestions();
}
const clearEmployeeSuggestions = () => {
    employeeSuggestions.value = [];
}
const runEmployeeSearch = ref(true);
const resetRunSearch = () => {
    if (searchEmployee.value) {
        runEmployeeSearch.value = true;
    }
}
const filteredServices = computed(() => {
    if (searchEmployee.value != "") {
        return props.services.filter((service) => service.serviceName.toLowerCase().includes(searchEmployee.value.toLowerCase()));
    } else {
        console.log("Input field has no content");
        return props.services;
    }
});

const isVisible = ref(false);
const addServiceRecord = (service) => {
    axios.post(route('services.store'), {
        serviceName: service,
    });
    Inertia.visit(route('services.index'));
}
</script>
<template>
    <Head title="Services Offered" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">View Services</h2>
        </template>
        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <AddServicesModal
                :services="props.services"
                @confirmSubmission="addServiceRecord" 
                @close="isVisible = false"
                ref='component' className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>
        <div class="py-6 w-full">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <Link
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            :href="route('job_orders.main')">
                        Back
                        </Link>
                    </div>
                </div>
                <div className="flex items-center">
                    <button
                        className="w-full flex justify-center px-6 py-2 text-white bg-dark-pastel-green text-3xl font-black rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
                        @click="isVisible = true">
                    +
                    </button>
                    <!--<Link
                        className="w-full flex justify-center px-6 py-2 text-white bg-dark-pastel-green text-3xl font-black rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
                        :href="route('employees.create')">
                    +
                    </Link>-->
                </div>
                <div style="max-height: calc(90vh - 16rem);" class="overflow-y-auto">
                    <div className="grid figtree grid-cols-3 gap-2 ">
                        <div v-for="service in filteredServices" :key="service.id">
                            <div className="flex flex-col justify-center items-center w-full bg-ghost-white px-10 py-4 border-b border-gray-200 sm:rounded-lg">
                                <div className="flex flex-row w-full">
                                    <div class="font-bold text-xl w-9/12 line-clamp-2" style="display: inline;">
                                        {{ service.serviceName }}
                                    </div>
                                    <div class="font-bold text-xl text-right w-3/12">
                                       {{ formatServiceID(service.id) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
.icon {
    color: #BFBFBF;
    /* Default icon color */
}

#searchInput::placeholder {
    font-style: italic;
    font-size: 0.9rem;
}
</style>

