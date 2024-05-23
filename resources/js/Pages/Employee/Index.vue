<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed } from 'vue';
const props = defineProps({
    employees: Array,
});
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
const filteredEmployees = computed(() => {
    if (searchEmployee.value != "") {
        return props.employees.filter((employee) => employee.employeeName.toLowerCase().includes(searchEmployee.value.toLowerCase()));
    } else {
        console.log("Input field has no content");
        return props.employees;
    }
});

</script>
<template>
    <Head title="Employee Management" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">Employee Management</h2>
        </template>

        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div className="flex items-center">
                    <Link
                        className="w-full flex justify-center px-6 py-2 text-white bg-dark-pastel-green text-3xl font-black rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
                        :href="route('employees.create')">
                    +
                    </Link>
                </div>
                <div style="max-height: calc(90vh - 16rem);" class="overflow-y-auto">
                    <div className="grid figtree grid-cols-2 gap-2 ">
                        <div v-for="employee in filteredEmployees" :key="employee.id">
                            <Link :href="route('employees.edit', employee.id)"
                                class="flex flex-row w-full h-40 bg-ghost-white px-4 py-4 border-b border-gray-200 sm:rounded-lg hover:bg-silver transition-all duration-300 ease-in-out">
                            <div class="flex items-center">
                                <font-awesome-icon :icon="['fas', 'user']" class="icon-2 w-24 h-24" />
                            </div>
                            <div className="px-4 pt-2 flex flex-col items-center">
                                <div className="flex flex-row w-full h-8/12">
                                    <div class="font-bold text-xl" style="display: inline;">{{ employee.firstName }}
                                    </div>
                                    <span>&nbsp;</span>
                                    <div class="font-bold text-xl" style="display: inline;">{{ employee.lastName }}
                                    </div>
                                </div>
                                <div className="w-full italic text-base h-1/12 leading-3">
                                    <span>{{ employee.position }}</span>
                                </div>
                                <div className="w-full h-1/12 pt-4">
                                    <div className="w-full italic text-sm leading-none">
                                        <span>{{ employee.number }}</span>
                                    </div>
                                    <div className="w-full italic text-sm">
                                        <span>{{ employee.email }}</span>
                                    </div>
                                    <div className="w-full line-clamp-1 leading-none">
                                        <span>{{ employee.address || 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
.icon-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #F7F8FF;
    /* Default background color */
    border-radius: 20%;
    /* Creates a circular shape */
}

.icon-container:hover {
    background-color: #CDCDCD;
}

.icon-2 {
    color: #516AC4;
    /* Default icon color */
}
</style>

