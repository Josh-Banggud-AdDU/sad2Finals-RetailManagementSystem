<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue'
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import { Head, Link, useForm, defineProps } from '@inertiajs/inertia-vue3';
import { ref, watch, onMounted, onUnmounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const props = defineProps({
    inventories: Array,
    suppliers: Array
})

const form = useForm({
    itemSerialNumber: '',
    itemName: '',
    itemCategory: '',
    itemModel: '',
    itemUnit: '',
    itemThreshold: '',
    itemCostPrice: '',
    itemSRP: '',
    supplierID: '',
    supplierName: '',
    itemMarkup: ''
});

const searchName = ref("");
const suggestions = ref([]);
const searchSupplier = async () => {

    const response = await fetch(`/search_suppliers?searchName=${searchName.value}`);
    const data = await response.json();

    const uniqueSupplierNames = new Set();
    suggestions.value = data.filter((suggestion) => {
    if (!uniqueSupplierNames.has(suggestion.supplierName)) {
        uniqueSupplierNames.add(suggestion.supplierName);
        return true;
    }
    return false;
    });
    
    if (searchName.value != "" && suggestions.value.length == 0) {
        suggestions.value.push('Supplier Not Found: Add Supplier');
    }
}
const isVisible = ref(false);
const selectSuggestion = (suggestion) => {
    if(suggestion == "Supplier Not Found: Add Supplier"){
        isVisible.value = true;
        suggestion.value = null;
    }else{
        searchName.value = suggestion.supplierName;
    }
    clearSuggestions();
}
const clearSuggestions = () => {
    suggestions.value = [];
}
const runSupplierSearch = ref(true);
const resetRunSearch = () => {
    if (searchName.value) {
        runSupplierSearch.value = true;
    }
}

const addSupplierRecord = async (supplierName) => {
    searchName.value = await supplierName;
    form.supplierName = supplierName;
    isVisible.value = false;

    try{
        form.post(route('suppliers.store'));
    } catch(error){
        console.log('Error: ' + error);
    }
    //form.post(route('suppliers.store'));
}
const closeSupplierModal = () => {
    isVisible.value = false;
    searchName.value = "";
}

const isVisible2 = ref(false);
const isVisible3 = ref(false);
const isVisible4 = ref(false);
const submit = async () => {
    if(
        form.itemName == '' ||
        form.itemUnit == '' ||
        form.itemSerialNumber == '' ||
        !!props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber) ||
        form.itemCategory == '' ||
        searchName.value == '' ||
        !props.suppliers.find((supplier) => supplier.supplierName == searchName.value) ||
        form.itemThreshold == '' ||
        isNaN(form.itemThreshold) ||
        form.itemThreshold < 1 ||
        form.itemCostPrice == '' ||
        isNaN(form.itemCostPrice) ||
        form.itemCostPrice <= 0 ||
        form.itemSRP == '' ||
        isNaN(form.itemSRP) ||
        form.itemSRP < ((parseFloat(form.itemCostPrice) * 0.15) + parseFloat(form.itemCostPrice))
    ){
        console.log('invalid');
        isVisible2.value = true;
        displayInvalid();
    }else if(
        form.itemName !== '' &&
        form.itemUnit !== '' &&
        form.itemSerialNumber !== '' &&
        !props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber) &&
        form.itemCategory !== '' &&
        searchName.value !== '' &&
        !!props.suppliers.find((supplier) => supplier.supplierName == searchName.value) &&
        form.itemThreshold !== '' &&
        !isNaN(form.itemThreshold) &&
        form.itemThreshold >= 1 &&
        form.itemCostPrice !== '' &&
        !isNaN(form.itemCostPrice) &&
        form.itemCostPrice > 0 &&
        form.itemSRP !== '' &&
        !isNaN(form.itemSRP) &&
        form.itemSRP >= ((parseFloat(form.itemCostPrice) * 0.15) + parseFloat(form.itemCostPrice))
    ){
        console.log('valid');
        isVisible3.value = true;
    }
};

const closeWarning = () => {
    isVisible2.value = false;
    isVisible3.value = false;
    isVisible4.value = false;
}

const itemSupplier = ref(null);
const confirm = async () => {
    isVisible3.value = false;
    itemSupplier.value = await props.suppliers.find((supplier) => supplier.supplierName.toLowerCase() == searchName.value.toLowerCase());
    form.supplierID = await itemSupplier.value.id;
    form.post(route('inventories.store'));
}

const exit = () => {
    Inertia.visit(route('inventories.index'));
}

const displayInvalid = (event) => {
    const id = event ? event.target.id : null;
    if(id == "itemName"){
        if(form.itemName == ''){
        form.errors.itemName = 'Invalid: Enter a value'
        }else{
            form.errors.itemName = '';
        }
    }else if(id == "itemUnit" || id == "unitButton"){
        if(form.itemUnit == ''){
            form.errors.itemUnit = 'Invalid: Enter a value'
        }else{
            form.errors.itemUnit = '';
        }
    }else if(id == "itemSerialNumber"){
        if(form.itemSerialNumber == ''){
            form.errors.itemSerialNumber = 'Invalid: Enter a value';
        }else if(!!props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber)){
            form.errors.itemSerialNumber = 'Invalid: Existing serial number';
        }else{
            form.errors.itemSerialNumber = '';
        }
    }else if(id == "itemCategory" || id == "categoryButton"){
        if(form.itemCategory == ''){
            form.errors.itemCategory = 'Invalid: Enter a value'
        }else{
            form.errors.itemCategory = '';
        }
    }else if(id == "itemSupplier" || id == "supplierButton"){
        if(searchName.value == ''){
            form.errors.itemSupplier = 'Invalid: Enter a value'
        }else if(!props.suppliers.find((supplier) => supplier.supplierName == searchName.value)){
            form.errors.itemSupplier = 'Invalid: Supplier does not exist'
        }else{
            form.errors.itemSupplier = '';
        }
    }else if(id == "itemThreshold"){
        if(form.itemThreshold == ''){
            form.errors.itemThreshold = 'Invalid: Enter a value';
        }else if(isNaN(form.itemThreshold)){
            form.errors.itemThreshold = 'Invalid: Enter a numeric value';
        }else if(form.itemThreshold < 1){
            form.errors.itemThreshold = 'Invalid: Enter a value greater than or equal to 1'
        }else{
            form.errors.itemThreshold = '';
        }
    }else if(id == "itemCostPrice"){
        if(form.itemCostPrice == ''){
            form.errors.itemCostPrice = 'Invalid: Enter a value';
        }else if(isNaN(form.itemCostPrice)){
            form.errors.itemCostPrice = 'Invalid: Enter a numeric value';
        }else if(form.itemCostPrice <= 0){
            form.errors.itemCostPrice = 'Invalid: Enter a value greater than 0'
        }else{
            form.errors.itemCostPrice = '';
        }
    }else if(id == "itemSRP"){
        if(form.itemSRP == ''){
            form.errors.itemSRP = 'Invalid: Enter a value';
        }else if(isNaN(form.itemSRP)){
            form.errors.itemSRP = 'Invalid: Enter a numeric value';
        }else if(form.itemSRP < ((parseFloat(form.itemCostPrice) * 0.15) + parseFloat(form.itemCostPrice))){
            form.errors.itemSRP = 'Invalid: Enter a value greater than the cost price plus 15%'
        }else{
            form.errors.itemSRP = '';
        }
    }else if(id == null){
        if(form.itemName == ''){
        form.errors.itemName = 'Invalid: Enter a value'
        }else{
            form.errors.itemName = '';
        }
        if(form.itemUnit == ''){
            form.errors.itemUnit = 'Invalid: Enter a value'
        }else{
            form.errors.itemUnit = '';
        }
        if(form.itemSerialNumber == ''){
            form.errors.itemSerialNumber = 'Invalid: Enter a value';
        }else if(!!props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber)){
            form.errors.itemSerialNumber = 'Invalid: Existing serial number';
        }else{
            form.errors.itemSerialNumber = '';
        }
        if(form.itemCategory == ''){
            form.errors.itemCategory = 'Invalid: Enter a value'
        }else{
            form.errors.itemCategory = '';
        }
        if(searchName.value == ''){
            form.errors.itemSupplier = 'Invalid: Enter a value'
        }else if(!props.suppliers.find((supplier) => supplier.supplierName == searchName.value)){
            form.errors.itemSupplier = 'Invalid: Supplier does not exist'
        }else{
            form.errors.itemSupplier = '';
        }
        if(form.itemThreshold == ''){
            form.errors.itemThreshold = 'Invalid: Enter a value';
        }else if(isNaN(form.itemThreshold)){
            form.errors.itemThreshold = 'Invalid: Enter a numeric value';
        }else if(form.itemThreshold < 1){
            form.errors.itemThreshold = 'Invalid: Enter a value greater than or equal to 1'
        }else{
            form.errors.itemThreshold = '';
        }
        if(form.itemCostPrice == ''){
            form.errors.itemCostPrice = 'Invalid: Enter a value';
        }else if(isNaN(form.itemCostPrice)){
            form.errors.itemCostPrice = 'Invalid: Enter a numeric value';
        }else if(form.itemCostPrice <= 0){
            form.errors.itemCostPrice = 'Invalid: Enter a value greater than 0'
        }else{
            form.errors.itemCostPrice = '';
        }
        if(form.itemSRP == ''){
            form.errors.itemSRP = 'Invalid: Enter a value';
        }else if(isNaN(form.itemSRP)){
            form.errors.itemSRP = 'Invalid: Enter a numeric value';
        }else if(form.itemSRP < ((parseFloat(form.itemCostPrice) * 0.15) + parseFloat(form.itemCostPrice))){
            form.errors.itemSRP = 'Invalid: Enter a value greater than the cost price plus 15%'
        }else{
            form.errors.itemSRP = '';
        }
    }
    calculateMarkup();
}

const unitDropdown = ref(false);
const categoryDropdown = ref(false);
const itemUnits = ref([]);
const itemCategories = ref([]);
const selectUnit = (choice) => {
    hoveredItem[choice.id] = false;
    form.itemUnit = choice.unit;
    unitDropdown.value = false;
}
const selectCategory = (choice) => {
    hoveredItem[choice.id] = false;
    form.itemCategory = choice.category;
    categoryDropdown.value = false;
}
const hoveredItem = reactive({});

const unitRef = ref(null);
const categoryRef = ref(null);
const supplierRef = ref(null);
onMounted( async () => {
    const response = await fetch(`/show_units`);
    const data = await response.json();
    itemUnits.value = data;

    const response2 = await fetch(`/show_categories`);
    const data2 = await response2.json();
    itemCategories.value = data2;

    const handleClickOutsideUnit = (event) => {
        if(unitRef.value && !unitRef.value.contains(event.target)){
            unitDropdown.value = false;
        }
    }
    const handleClickOutsideCategory = (event) => {
        if(categoryRef.value && !categoryRef.value.contains(event.target)){
            categoryDropdown.value = false;
        }
    }
    const handleClickOutsideSupplier = (event) => {
        if(supplierRef.value && !supplierRef.value.contains(event.target)){
            clearSuggestions();
        }
    }
    document.addEventListener('mousedown', handleClickOutsideUnit);
    document.addEventListener('mousedown', handleClickOutsideCategory);
    document.addEventListener('mousedown', handleClickOutsideSupplier);
    onUnmounted(() => {
        document.removeEventListener('mousedown', handleClickOutsideUnit);
        document.removeEventListener('mousedown', handleClickOutsideCategory);
        document.removeEventListener('mousedown', handleClickOutsideSupplier);
    });
})

const calculateMarkup = () => {
    if(form.itemCostPrice && form.itemSRP){
        form.itemMarkup = (((parseFloat(form.itemSRP) - parseFloat(form.itemCostPrice)) / parseFloat(form.itemCostPrice)) * 100).toFixed(2);
    }else{
        form.itemMarkup = (0).toFixed(2);
    }
}
</script>
<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                Create Item
            </h2>
        </template>
        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <AddSupplierModal
                :suppliers="props.suppliers"
                @confirmSubmission="addSupplierRecord" 
                @close="closeSupplierModal"
                ref='component' className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>
        <div v-if="isVisible2" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningIncompleteModal @close="closeWarning" />
        </div>
        <div v-if="isVisible3" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmInputModal @confirmSubmission="confirm" @close="closeWarning" />
        </div>
        <div v-if="isVisible4" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmBackModal @confirmSubmission="exit" @close="closeWarning" />
        </div>
        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <button type="button"
                            @click="isVisible4 = true"
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out">
                            Back
                        </button>
                        <!--<Link
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            :href="route('inventories.index')">
                        Back
                        </Link>-->
                    </div>
                </div>
                <div class="shadow-sm">
                    <div class="p-6 bg-ghost-white noto-sans border-b border-gray-200 rounded-lg">
                        <form name="createForm" @submit.prevent="submit">
                            <div className="flex flex-col">
                                <div className="flex flex-row w-full figtree mb-6 space-x-4">
                                    <div className="relative w-11/12">
                                        <BreezeLabel for="itemName" value="Item Name" />

                                        <BreezeTextInput id="itemName" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemName" @input="displayInvalid" autofocus autocomplete=off />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.itemName">
                                            {{ form.errors.itemName }}
                                        </span>
                                    </div>

                                    <div className="relative w-1/12" ref="unitRef">
                                        <BreezeLabel for="itemUnit" value="Unit" />

                                        <BreezeTextInput id="itemUnit" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemUnit" @input="displayInvalid" @focus="unitDropdown = true" autocomplete=off />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.itemUnit">
                                            {{ form.errors.itemUnit }}
                                        </span>
                                        <ul v-if="unitDropdown"
                                            className="absolute mt-2 w-full bg-ghost-white text-savoy-blue max-h-48 overflow-y-auto font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                            <button id="unitButton" @blur="displayInvalid" type="button" v-for="(unit, key) in itemUnits" :key="key"
                                                @click="selectUnit({unit, id: key})" @mouseover="hoveredItem[key] = true"
                                                @mouseout="hoveredItem[key] = false"
                                                :class="{ 'bg-ghost-white text-savoy-blue': !hoveredItem[key], 'bg-silver': hoveredItem[key]}"
                                                class="pl-2 text-left w-full">
                                                {{ unit }}
                                            </button>
                                        </ul>
                                    </div>
                                </div>
                                <div className="flex flex-row w-full mb-6 space-x-4">
                                    <div className="relative w-1/4">
                                        <BreezeLabel for="itemSerialNumber" value="Serial Number" />

                                        <BreezeTextInput id="itemSerialNumber" type="input"
                                            class="mt-1 px-2 py-1 block w-full" v-model="form.itemSerialNumber"
                                            @input="displayInvalid" autocomplete=off />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.itemSerialNumber">
                                            {{ form.errors.itemSerialNumber }}
                                        </span>
                                    </div>
                                    <div className="relative w-2/4" ref="categoryRef">
                                        <BreezeLabel for="itemCategory" value="Category" />

                                        <BreezeTextInput id="itemCategory" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemCategory" @input="displayInvalid" @focus="categoryDropdown = true" autocomplete=off />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.itemCategory">
                                            {{ form.errors.itemCategory }}
                                        </span>
                                        <ul v-if="categoryDropdown"
                                            className="absolute mt-2 w-full bg-ghost-white text-savoy-blue max-h-48 overflow-y-auto font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                            <button id="categoryButton" @blur="displayInvalid" type="button" v-for="(category, key) in itemCategories" :key="key"
                                                @click="selectCategory({category, id: key})" @mouseover="hoveredItem[key] = true"
                                                @mouseout="hoveredItem[key] = false"
                                                :class="{ 'bg-ghost-white text-savoy-blue': !hoveredItem[key], 'bg-silver': hoveredItem[key]}"
                                                class="pl-2 text-left w-full">
                                                {{ category }}
                                            </button>
                                        </ul>
                                    </div>
                                    <div className="w-1/4">
                                        <BreezeLabel for="itemModel" value="Model" />

                                        <BreezeTextInput id="itemModel" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemModel" autocomplete=off />
                                    </div>
                                </div>
                                <div className="relative mb-6 w-full" ref="supplierRef">
                                    <BreezeLabel for="itemSupplier" value="Supplier Name" />
                                    <div class="flex f  lex-row items-center py-1 pr-2 bg-white rounded-lg shadow-sm">
                                        <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                        <div class="relative w-full">
                                            <BreezeTextInput id="itemSupplier" type="input" class="px-2 py-1 block w-full shadow-none"
                                                v-model="searchName" @input="searchSupplier" @blur="displayInvalid" @click="resetRunSearch" 
                                                autocomplete=off />
                                            <ul v-if="suggestions.length != 0"
                                                className=" absolute mt-2 w-full bg-ghost-white max-h-48 overflow-y-auto text-savoy-blue font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                                <button id="supplierButton" @blur="displayInvalid" type="button" v-for="suggestion in suggestions" :key="suggestion.id"
                                                    @click="selectSuggestion(suggestion)" className="text-left w-full pl-2 hover:bg-silver"
                                                    :style="{ fontStyle: suggestion === 'Supplier Not Found: Add Supplier' ? 'italic' : 'normal' }"
                                                    >
                                                    {{ suggestion.supplierName ? suggestion.supplierName : suggestion }}
                                                </button>
                                            </ul>
                                        </div>
                                    </div>
                                    <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.itemSupplier">
                                        {{ form.errors.itemSupplier }}
                                    </span>
                                </div>
                                <div className="flex flex-row w-full space-x-4 mb-8">
                                    <div className="relative w-1/6 flex flex-col">
                                        <BreezeLabel for="itemThreshold" value="Minimum Stock Threshold" />

                                        <BreezeTextInput id="itemThreshold" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemThreshold" @input="displayInvalid" autocomplete=off />
                                        <span className="error-message-2 text-red-600 text-sm text-center absolute w-full top-full left-0" v-if="form.errors.itemThreshold">
                                            {{ form.errors.itemThreshold }}
                                        </span>
                                    </div>
                                    <div className="w-1/6">
                                        <BreezeLabel for="itemCostPrice" value="Item Cost Price" />
                                        <div className="flex flex-row items-center space-x-2 relative">
                                            <span>
                                                ₱
                                            </span>
                                            <BreezeTextInput id="itemCostPrice" type="input"
                                                class="mt-1 px-2 py-1 block w-full" v-model="form.itemCostPrice"
                                                @input="displayInvalid" autocomplete=off />
                                            <span className="error-message-2 text-red-600 text-sm text-center absolute w-full top-full left-0" v-if="form.errors.itemCostPrice">
                                                {{ form.errors.itemCostPrice }}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="w-1/6">
                                        <BreezeLabel for="itemSRP" value="Suggested Retail Price" />
                                        <div className="flex flex-row items-center space-x-2 relative">
                                            <span>
                                                ₱
                                            </span>
                                            <BreezeTextInput id="itemSRP" type="input" class="mt-1 px-2 py-1 block w-full"
                                                @input="displayInvalid" v-model="form.itemSRP" autocomplete=off />
                                            <span className="error-message-2 text-red-600 text-sm text-center absolute w-full top-full left-0" v-if="form.errors.itemSRP">
                                                {{ form.errors.itemSRP }}
                                            </span>
                                        </div>
                                    </div>
                                    <div className="w-1/6">
                                        <BreezeLabel for="itemMarkup" value="Item Markup Value" />
                                        <div className="flex flex-row items-center space-x-2">
                                            <BreezeTextInput id="itemMarkup" type="input"
                                                class="mt-1 px-2 py-1 block w-full" v-model="form.itemMarkup" disabled />
                                            <span>
                                                %
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="mt-4">
                                <button type="submit" className="px-6 py-2 font-bold font-montserrat text-white bg-dark-pastel-green rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<style scoped>
.error-message {
  display: block;
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  /* Keep the error message on one line */
}
.error-message-2 {
  display: inline-block;
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  /* Keep the error message on one line */
}
.icon {
    color: #BFBFBF;
    /* Default icon color */
}
</style>