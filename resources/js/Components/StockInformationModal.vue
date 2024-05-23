<script setup>

import { ref, defineEmits, onMounted, onUnmounted } from 'vue';
import { useForm, defineProps } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';

const { employees, logs, transaction, suppliers, sales } = defineProps(['employees', 'logs', 'transaction', 'suppliers', 'sales']);
const confirmHovered = ref(false);
const cancelHovered = ref(false);
const emit = defineEmits();
const form = useForm({
    employeeName: '',
    employeeID: '',
    referenceNum: '',
})

const isVisible = ref(true);
const searchRef = ref(null);
const employeeRef = ref(null);
onMounted( async () => {
    console.log(sales);
    if(transaction == "Stock Out" || transaction == "Item Return") {
        isVisible.value = false;
    }else if(transaction == "Stock In"){
        isVisible.value = true;
    }

    const handleClickOutsideSearch = (event) => {
    if (searchRef.value && !searchRef.value.contains(event.target)) {
        clearSupplierSuggestions();
        }
    };
    const handleClickOutsideEmployee = (event) => {
    if (employeeRef.value && !employeeRef.value.contains(event.target)) {
        clearSuggestions();
        }
    };
    document.addEventListener('mousedown', handleClickOutsideSearch);
    document.addEventListener('mousedown', handleClickOutsideEmployee);
    onUnmounted(() => {
        document.removeEventListener('mousedown', handleClickOutsideSearch);
        document.removeEventListener('mousedown', handleClickOutsideEmployee);
    });
})

const isOpen = ref(false);
const suggestions = ref([]);
const searchEmployee = () => {
    suggestions.value = employees;
    isOpen.value = true;
}
const selectSuggestion = (suggestion) => {
    form.employeeName = suggestion.firstName + " " + suggestion.lastName;
    form.employeeID = suggestion.id;
    console.log(form.employeeName, form.employeeID);
    clearSuggestions();
}
const clearSuggestions = () => {
    suggestions.value = [];
    isOpen.value = false;
}

const searchName = ref("");
const supplierSuggestions = ref([]);
const searchSupplier = async () => {
    const response = await fetch(`/search_suppliers?searchName=${searchName.value}`);
    const data = await response.json();

    const uniqueSupplierNames = new Set();
    supplierSuggestions.value = data.filter((suggestion) => {
    if (!uniqueSupplierNames.has(suggestion.supplierName)) {
        uniqueSupplierNames.add(suggestion.supplierName);
        return true;
    }
    return false;
    });
}
const supplierID = ref("");
const selectSupplierSuggestion = (suggestion) => {
    searchName.value = suggestion.supplierName;
    supplierID.value = suggestion.id;
    clearSupplierSuggestions();
}
const clearSupplierSuggestions = () => {
    supplierSuggestions.value = [];
}
const runSupplierSearch = ref(true);
const resetRunSearch = () => {
    if (searchName.value) {
        runSupplierSearch.value = true;
    }
}
const closeWarning = () => {
    isVisible2.value = false;
}

const exit = () => {
    isVisible2.value = false;
    Inertia.visit(route('inventories.manage_supply'));
}
const isVisible2 = ref(false);
const cancel = () => {
    isVisible2.value = true;
}
const confirmAction = () => {
    if(transaction == 'Stock In'){
        if(
            form.employeeName == '' || 
            searchName.value == '' ||
            !suppliers.find((supplier) => supplier.supplierName == searchName.value) ||
            form.referenceNum == '' || 
            !!logs.find((log) => log.referenceNum == form.referenceNum)
        ){
            displayInvalid();
        }
        else if(
            form.employeeName != '' && 
            searchName.value != '' &&
            !!suppliers.find((supplier) => supplier.supplierName == searchName.value) &&
            form.referenceNum != '' && 
            !logs.find((log) => log.referenceNum == form.referenceNum)
        ){
            emit("confirmSubmission", form.employeeID, form.referenceNum, supplierID.value);
        }
    }else if(transaction == 'Stock Out'){
        if(
            form.employeeName == '' || 
            form.referenceNum == '' || 
            !!logs.find((log) => log.referenceNum == form.referenceNum)
        ){
            displayInvalid();
        }
        else if(
            form.employeeName != '' && 
            form.referenceNum != '' && 
            !logs.find((log) => log.referenceNum == form.referenceNum)
        ){
            emit("confirmSubmission", form.employeeID, form.referenceNum, supplierID.value);
        }
    }else if(transaction == 'Item Return'){
        if(
            form.employeeName == '' || 
            isNaN(form.salesID) ||
            form.salesID == '' || 
            !sales.find((sale) => sale.id == form.salesID)
        ){
            displayInvalid();
        }
        else if(
            form.employeeName != '' && 
            !isNaN(form.salesID) &&
            form.salesID != '' && 
            !!sales.find((sale) => sale.id == form.salesID)
        ){
            emit("confirmSubmission", form.employeeID, form.salesID, supplierID.value);
        }
    }
}

const displayInvalid = (event) => {
    const id = event ? event.target.id : null;
    if(id == "supplierName" || id =="supplierButton"){
        if(searchName.value == ''){
            form.errors.supplierName = 'Invalid: Enter a value';
        }else if(!suppliers.find((supplier) => supplier.supplierName == searchName.value)){
            form.errors.supplierName = 'Invalid: Supplier does not exist';
        }else{
            form.errors.supplierName = '';
        }
    }else if(id == 'employeeName' || id == "employeeButton"){
        if(form.employeeName == ''){
            form.errors.employeeName = 'Invalid: Select a value';
        }else{
            form.errors.employeeName = '';
        }
    }else if(id == 'referenceNumber'){
        if(form.referenceNum == ''){
            form.errors.referenceNum = 'Invalid: Enter a value';
        }else if(logs.find((log) => log.referenceNum == form.referenceNum)){
            form.errors.referenceNum = 'Invalid: Existing reference number';
        }else{
            form.errors.referenceNum = '';
        }
    }else if(id == 'salesReferenceNumber'){
        if(form.salesID == ''){
            form.errors.salesID = 'Invalid: Enter a value';
        }else if(isNaN(form.salesID)){
            form.errors.salesID = 'Invalid: Enter a numeric value';
        }else if(!sales.find((sale) => sale.id == form.salesID)){
            form.errors.salesID = 'Invalid: Non-existent sales order';
        }else{
            form.errors.salesID = '';
        }
    }else if(id == null){
        if(searchName.value == ''){
            form.errors.supplierName = 'Invalid: Enter a value';
        }else if(!suppliers.find((supplier) => supplier.supplierName == searchName.value)){
            form.errors.supplierName = 'Invalid: Supplier does not exist';
        }else{
            form.errors.supplierName = '';
        }
        if(form.employeeName == ''){
            form.errors.employeeName = 'Invalid: Select a value';
        }else{
            form.errors.employeeName = '';
        }
        if(form.referenceNum == ''){
            form.errors.referenceNum = 'Invalid: Enter a value';
        }else if(logs.find((log) => log.referenceNum == form.referenceNum)){
            form.errors.referenceNum = 'Invalid: Existing reference number';
        }else{
            form.errors.referenceNum = '';
        }
        if(form.salesID == ''){
            form.errors.salesID = 'Invalid: Enter a value';
        }else if(isNaN(form.salesID)){
            form.errors.salesID = 'Invalid: Enter a numeric value';
        }else if(!sales.find((sale) => sale.id == form.salesID)){
            form.errors.salesID = 'Invalid: Non-existent sales order';
        }else{
            form.errors.salesID = '';
        }
    }
}
</script>

<template>
    <div class="flex justify-center items-center h-screen">
        <div v-if="isVisible2" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmBackModal @confirmSubmission="exit" @close="closeWarning" />
        </div>
        <div class="w-full">
            <div class="mx-auto rounded-lg bg-ghost-white">
                <div class="bg-savoy-blue text-white p-4 rounded-t-lg">
                    <h2 class="text-2xl font-bold font-montserrat text-center">Stock Management Details</h2>
                </div>
                <div class="flex p-3 px-8 pt-4 pb-6 flex-col figtree">
                    <div class="text-lg px-4">
                        <div class="relative mb-4" ref="employeeRef">
                            <BreezeLabel for="employeeName" value="Employee Name" class="text-lg figtree pt-4" />
                            <div class="flex flex-row items-center py-1 pr-2 bg-white rounded-lg shadow-sm">
                                <div class="relative w-full">
                                    <BreezeTextInput id="employeeName" type="input"
                                        class="mt-1 px-2 py-1 block w-full rounded-md figtree" v-model="form.employeeName"
                                        @focus="searchEmployee" @blur="displayInvalid" autofocus readonly />
                                    <ul v-if="suggestions && suggestions.length != 0"
                                        className=" absolute mt-2 w-full bg-ghost-white max-h-48 overflow-y-auto text-savoy-blue font-bold border border-gray-300 rounded-lg shadow-lg z-40 p-2">
                                        <button id="employeeButton" type="button" v-for="suggestion in suggestions" :key="suggestion.id"
                                            @click="selectSuggestion(suggestion)" @blur="displayInvalid"
                                            className="text-left w-full pl-2 hover:bg-silver">
                                            {{ suggestion.firstName + " " + suggestion.lastName }}
                                        </button>
                                    </ul>
                                </div>
                                <font-awesome-icon id="dropdownIcon" :icon="['fas', 'caret-down']" class="icon w-5 h-5 p-2 transform transition duration-300" :class="{
                                    '-rotate-180' : isOpen,
                                    '-rotate-0' : !isOpen
                                    }"/>
                            </div>
                            <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.employeeName">
                                {{ form.errors.employeeName }}
                            </span>
                        </div>
                        <div v-if="isVisible" class="relative mb-4" ref="searchRef">
                            <BreezeLabel for="supplierName" value="Supplier Name" class="text-lg figtree pt-4" />
                            <div class="flex flex-row items-center py-1 pr-2 bg-white rounded-lg shadow-sm">
                                <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                <div class="relative w-full">
                                    <BreezeTextInput id="supplierName" type="input"
                                        class="mt-1 px-2 py-1 block w-full rounded-md figtree" v-model="searchName"
                                        @input="searchSupplier" @click="resetRunSearch" @blur="displayInvalid" autocomplete="off"/>
                                    <ul v-if="supplierSuggestions.length != 0"
                                        className=" absolute mt-2 w-full bg-ghost-white max-h-48 overflow-y-auto text-savoy-blue font-bold border border-gray-300 rounded-lg shadow-lg z-40 p-2">
                                        <button id="supplierButton" type="button" v-for="suggestion in supplierSuggestions" :key="suggestion.id"
                                            @click="selectSupplierSuggestion(suggestion)" @blur="displayInvalid"
                                            className="text-left w-full pl-2 hover:bg-silver">
                                            {{ suggestion.supplierName}}
                                        </button>
                                    </ul>
                                </div>
                            </div>
                            <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.supplierName">
                                {{ form.errors.supplierName }}
                            </span>
                        </div>
                        <div class="mb-8">
                            <div v-if="transaction == 'Stock In' || transaction == 'Stock Out'" class="relative">
                            <BreezeLabel for="referenceNumber" value="Supply Reference Number" class="text-lg figtree pt-4" />
                            <BreezeTextInput id="referenceNumber" type="input" @blur="displayInvalid"
                                class="mt-1 px-2 py-1 block w-96 rounded-md figtree" v-model="form.referenceNum"
                                autocomplete="off"/>
                            <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.referenceNum">
                                {{ form.errors.referenceNum }}
                            </span>
                            </div>
                            <div v-if="transaction == 'Item Return'"  class="relative mb-8">
                                <BreezeLabel for="salesReferenceNumber" value="Sales Order ID" class="text-lg figtree pt-4" />
                                <BreezeTextInput id="salesReferenceNumber" type="input" @blur="displayInvalid"
                                    class="mt-1 px-2 py-1 block w-96 rounded-md figtree" v-model="form.salesID"
                                    autocomplete="off"/>
                                <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.salesID">
                                    {{ form.errors.salesID }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-center space-x-6 items-center pt-3 gap-4">
                        <button @click="confirmAction" 
                        class="btn px-5 py-2 font-bold font-montserrat text-white bg-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue transition-all duration-300 ease-in-out">
                            Confirm
                        </button>
                        <button @click="cancel"
                        class="btn px-6 py-2 font-bold font-montserrat text-savoy-blue bg-ghost-white border border-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue hover:text-ghost-white transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
  
<style scoped>
.icon {
    color: #BFBFBF;
    /* Default icon color */
}
.error-message {
  display: block;
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  /* Keep the error message on one line */
}

</style>