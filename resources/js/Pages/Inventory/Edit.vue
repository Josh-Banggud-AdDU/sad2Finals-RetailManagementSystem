<script setup>
import { ref } from 'vue';
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeFloatInput from '@/Components/FloatInput.vue';
import BreezeCheckbox from '@/Components/Checkbox.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue'
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { onMounted, onUnmounted, computed, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const props = defineProps({
    inventory: Object,
    suppliers: Array,
    inventory_history: Array,
    inventories: Array
});
const form = useForm({
    id: props.inventory.id,
    itemSerialNumber: props.inventory.itemSerialNumber,
    itemName: props.inventory.itemName,
    itemCategory: props.inventory.itemCategory,
    itemModel: props.inventory.itemModel,
    itemLocation: props.inventory.itemLocation,
    itemQuantity: props.inventory.itemQuantity,
    itemUnit: props.inventory.itemUnit,
    itemThreshold: props.inventory.itemThreshold,
    itemCostPrice: props.inventory.itemCostPrice,
    itemSRP: props.inventory.itemSRP,
    itemMarkup: props.inventory.itemMarkup,
    isNotifying: props.inventory.isNotifying,
    isDisabled: props.inventory.isDisabled,
    supplierID: props.inventory.supplierID,
});

const itemSupplierID = ref(props.suppliers.find((supplier) => supplier.id == form.supplierID));
const searchName = ref(null);
searchName.value = itemSupplierID.value.supplierName;
const suggestions = ref([]);
const addRecord = ref(0);
const searchSupplier = async () => {
    if (searchName.value != "" && runSupplierSearch.value == true) {
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
    }
    if (searchName.value != "" && suggestions.value.length == 0) {
        console.log('Supplier Not Found: Add Supplier');
        suggestions.value.push('Supplier Not Found: Add Supplier');
    }
}
const isVisible = ref(false);
const selectSuggestion = (suggestion) => {
    if (suggestion == "Supplier Not Found: Add Supplier") {
        isVisible.value = true;
    } else {
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

    try {
        await axios.post(route('suppliers.store'), form);
    } catch (error) {
        console.log('Error: ' + error);
    }
}
const closeSupplierModal = () => {
    isVisible.value = false;
    searchName.value = "";
}

const editable = ref(false);
const cancelEdit = () => {
    showCurrent();
    editable.value = false;
};

const isVisible2 = ref(false);
const isVisible3 = ref(false);
const isVisible4 = ref(false);
const submit = async () => {
    if(
        form.itemName == '' ||
        form.itemUnit == '' ||
        form.itemSerialNumber == '' ||
        !!props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber && item.id !== form.id) ||
        form.itemCategory == '' ||
        form.itemLocation == '' ||
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
    }else if(
        form.itemName !== '' &&
        form.itemUnit !== '' &&
        form.itemSerialNumber !== '' &&
        !props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber && item.id !== form.id) &&
        form.itemCategory !== '' &&
        form.itemLocation !== '' &&
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
    itemSupplier.value = await props.suppliers.find((supplier) => supplier.supplierName == searchName.value);
    form.supplierID = await itemSupplier.value.id;
    form.put(route('inventories.update', props.inventory.id));
}

const exit = () => {
    Inertia.visit(route('inventories.index'));
}

const reversedQuantityHistory = computed(() => {
    return [...props.inventory_history].reverse().filter((log) => {
        return log.generalNotes && log.itemID == props.inventory.id;
    });
});
const reversedEditHistory = computed(() => {
    return [...props.inventory_history].reverse().filter((log) => {
        return !log.generalNotes && log.itemID == props.inventory.id;
    });
});

const showEdit = ref(null);
const handleRowClick = (log) => {
    if(!editable.value){
        showEdit.value = log;
        form.itemSerialNumber = props.inventory_history.find((history) => history.id == log.id).itemSerialNumber;
        form.itemName = props.inventory_history.find((history) => history.id == log.id).itemName;
        form.itemCategory = props.inventory_history.find((history) => history.id == log.id).itemCategory;
        form.itemModel = props.inventory_history.find((history) => history.id == log.id).itemModel;
        form.itemLocation = props.inventory_history.find((history) => history.id == log.id).itemLocation;
        form.itemQuantity = props.inventory_history.find((history) => history.id == log.id).itemQuantity;
        form.itemUnit = props.inventory_history.find((history) => history.id == log.id).itemUnit;
        form.itemThreshold = props.inventory_history.find((history) => history.id == log.id).itemThreshold;
        form.itemCostPrice = props.inventory_history.find((history) => history.id == log.id).itemCostPrice;
        form.itemSRP = props.inventory_history.find((history) => history.id == log.id).itemSRP;
        form.itemMarkup = props.inventory_history.find((history) => history.id == log.id).itemMarkup;
        form.isNotifying = props.inventory_history.find((history) => history.id == log.id).isNotifying;
        form.isDisabled = props.inventory_history.find((history) => history.id == log.id).isDisabled;
        form.supplierID = props.inventory_history.find((history) => history.id == log.id).supplierID;
    }
}

const showCurrent = () => {
    showEdit.value = null;
    form.itemSerialNumber = props.inventory.itemSerialNumber;
    form.itemName = props.inventory.itemName;
    form.itemCategory = props.inventory.itemCategory;
    form.itemModel = props.inventory.itemModel;
    form.itemLocation = props.inventory.itemLocation;
    form.itemQuantity = props.inventory.itemQuantity;
    form.itemUnit = props.inventory.itemUnit;
    form.itemThreshold = props.inventory.itemThreshold;
    form.itemCostPrice = props.inventory.itemCostPrice;
    form.itemSRP = props.inventory.itemSRP;
    form.itemMarkup = props.inventory.itemMarkup;
    form.isNotifying = props.inventory.isNotifying;
    form.isDisabled = props.inventory.isDisabled;
    form.supplierID = props.inventory.supplierID;
}

const unitDropdown = ref(false);
const categoryDropdown = ref(false);
const locationDropdown = ref(false);
const itemUnits = ref([]);
const itemCategories = ref([]);
const itemLocations = ref([
    { location: 'Glass Shelf', id: 0 },
    { location: 'Hanging Wall', id: 1 },
    { location: 'Shelf A', id: 2 },
    { location: 'Shelf B', id: 3 },
    { location: 'Shelf C', id: 4 },
    { location: 'Shelf D', id: 5 },
    { location: 'Shelf E', id: 6 },
    { location: 'Shelf F', id: 7 },
    { location: 'Shelf G', id: 8 },
    { location: 'Shelf H', id: 9 },
    { location: 'Shelf I', id: 10 },
    { location: 'Shelf J', id: 11 },
    { location: 'Shelf K', id: 12 },
    { location: 'Stock Room', id: 13 },
]);
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
const selectLocation = (choice) => {
    form.itemLocation = choice.location;
    locationDropdown.value = false;
}
const hoveredItem = reactive({});

const unitRef = ref(null);
const categoryRef = ref(null);
const locRef = ref(null);
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
    const handleClickOutsideLoc = (event) => {
        if(locRef.value && !locRef.value.contains(event.target)){
            locationDropdown.value = false;
        }
    }
    const handleClickOutsideSupplier = (event) => {
        if(supplierRef.value && !supplierRef.value.contains(event.target)){
            clearSuggestions();
        }
    }
    document.addEventListener('mousedown', handleClickOutsideUnit);
    document.addEventListener('mousedown', handleClickOutsideCategory);
    document.addEventListener('mousedown', handleClickOutsideLoc);
    document.addEventListener('mousedown', handleClickOutsideSupplier);
    onUnmounted(() => {
        document.removeEventListener('mousedown', handleClickOutsideUnit);
        document.removeEventListener('mousedown', handleClickOutsideCategory);
        document.removeEventListener('mousedown', handleClickOutsideLoc);
        document.removeEventListener('mousedown', handleClickOutsideSupplier);
    });
})

/*const itemNameValid = computed(() => {
    if(form.itemName == ''){
        invalid.value = true;
        return 'Invalid: Enter a value';
    }
})*/

</script>
<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                {{ editable ? 'Edit Item' : 'View Item' }}
            </h2>
        </template>
        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <AddSupplierModal :suppliers="props.suppliers" @confirmSubmission="addSupplierRecord" @close="closeSupplierModal" ref='component'
                className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
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
                <div class="overflow-hidden shadow-sm">
                    <div class="p-6 bg-ghost-white noto-sans border-b border-gray-200 rounded-lg">
                        <form name="createForm" @submit.prevent="submit">
                            <div className="flex flex-col">
                                <div className="flex flex-row w-full mb-6 space-x-4">
                                    <div className="relative w-10/12">
                                        <BreezeLabel for="itemName" value="Item Name" />

                                        <BreezeTextInput id="itemName" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemName" :disabled="!editable" autofocus autocomplete="off" />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.itemName == ''">
                                            Invalid: Enter a value
                                        </span>
                                    </div>
                                    <div className="w-1/12">
                                        <BreezeLabel for="itemQuantity" value="Quantity" />

                                        <BreezeFloatInput id="itemQuantity" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemQuantity" :disabled="true" autocomplete="off" />
                                    </div>
                                    <div className="relative w-1/12" ref="unitRef">
                                        <BreezeLabel for="itemUnit" value="Unit" />

                                        <BreezeTextInput id="itemUnit" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemUnit" :disabled="!editable" @focus="unitDropdown = true" autocomplete="off" />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.itemUnit == ''">
                                            Invalid: Enter a value
                                        </span>
                                        <ul v-if="unitDropdown"
                                            className="absolute mt-2 w-full bg-ghost-white text-savoy-blue max-h-48 overflow-y-auto font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                            <button type="button" v-for="(unit, key) in itemUnits" :key="key"
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
                                    <div className="relative w-1/6">
                                        <BreezeLabel for="itemSerialNumber" value="Serial Number" />

                                        <BreezeTextInput id="itemSerialNumber" type="input"
                                            class="mt-1 px-2 py-1 block w-full" v-model="form.itemSerialNumber"
                                            :disabled="!editable" autofocus autocomplete="off" />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" 
                                            v-html="form.itemSerialNumber == ''
                                            ? 'Invalid: Enter a value'
                                            : props.inventories.find((item) => item.itemSerialNumber == form.itemSerialNumber && item.id !== form.id)
                                            ? 'Invalid: Existing serial number'
                                            : ''">
                                        </span>
                                    </div>
                                    <div className="relative w-2/6" ref="categoryRef">
                                        <BreezeLabel for="itemCategory" value="Category" />

                                        <BreezeTextInput id="itemCategory" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemCategory" :disabled="!editable" @focus="categoryDropdown = true" autofocus
                                            autocomplete="off" />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.itemCategory == ''">
                                            Invalid: Enter a value
                                        </span>
                                        <ul v-if="categoryDropdown"
                                            className="absolute mt-2 w-full bg-ghost-white text-savoy-blue max-h-48 overflow-y-auto font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                            <button type="button" v-for="(category, key) in itemCategories" :key="key"
                                                @click="selectCategory({category, id: key})" @mouseover="hoveredItem[key] = true"
                                                @mouseout="hoveredItem[key] = false"
                                                :class="{ 'bg-ghost-white text-savoy-blue': !hoveredItem[key], 'bg-silver': hoveredItem[key]}"
                                                class="pl-2 text-left w-full">
                                                {{ category }}
                                            </button>
                                        </ul>
                                    </div>
                                    <div className="relative w-2/6">
                                        <BreezeLabel for="itemModel" value="Model" />

                                        <BreezeTextInput id="itemModel" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemModel" :disabled="!editable" autofocus autocomplete="off" />
                                    </div>
                                    <div className="relative w-1/6" ref="locRef">
                                        <BreezeLabel for="itemLocation" value="Location" />

                                        <BreezeTextInput id="itemLocation" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.itemLocation" :disabled="!editable" @focus="locationDropdown = true" autofocus
                                            autocomplete="off" />
                                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.itemLocation == ''">
                                            Invalid: Enter a value
                                        </span>
                                        <ul v-if="locationDropdown"
                                            className="absolute mt-2 w-full bg-ghost-white text-savoy-blue max-h-48 overflow-y-auto font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                            <button type="button" v-for="loc in itemLocations" :key="loc.id"
                                                @click="selectLocation(loc)" @mouseover="loc.hovered = true"
                                                @mouseout="loc.hovered = false"
                                                :class="{ 'bg-ghost-white text-savoy-blue': !loc.hovered, 'bg-silver': loc.hovered}"
                                                class="pl-2 text-left w-full">
                                                {{ loc.location }}
                                            </button>
                                        </ul>
                                    </div>
                                </div>
                                <div className="relative mb-6" ref="supplierRef">
                                    <BreezeLabel for="itemSupplier" value="Supplier Name" />
                                    <div class="flex flex-row items-center py-1 pr-2 bg-white rounded-lg shadow-sm">
                                        <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                        <div class="relative w-full">
                                            <BreezeTextInput id="itemSupplier" type="input" class="px-2 py-1 block w-full shadow-none"
                                            v-model="searchName" :disabled="!editable" @input="searchSupplier"
                                            @click="resetRunSearch" autofocus autocomplete="off" />
                                            <ul v-if="suggestions.length != 0"
                                                className=" absolute mt-2 w-full bg-ghost-white max-h-48 overflow-y-auto text-savoy-blue font-bold border border-gray-300 rounded-lg shadow-lg z-50 p-2">
                                                <button type="button" v-for="suggestion in suggestions" :key="suggestion.id"
                                                    @click="selectSuggestion(suggestion)"
                                                    className="text-left w-full pl-2 hover:bg-silver"
                                                    :style="{ fontStyle: suggestion === 'Supplier Not Found: Add Supplier' ? 'italic' : 'normal' }">
                                                    {{ suggestion.supplierName ? suggestion.supplierName : suggestion }}
                                                </button>
                                            </ul>
                                        </div>
                                    </div>
                                    <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" 
                                        v-html="searchName.value == ''
                                        ? 'Invalid: Enter a value'
                                        : !props.suppliers.find((supplier) => supplier.supplierName == searchName)
                                        ? 'Invalid: Supplier does not exist'
                                        : ''"> 
                                    </span>
                                </div>
                                <div className="flex flex-row w-full space-x-4 mb-8">
                                    <div className="relative w-1/6">
                                        <BreezeLabel for="itemThreshold" value="Minimum Stock Threshold" />

                                        <BreezeFloatInput id="itemThreshold" type="input"
                                            class="mt-1 px-2 py-1 block w-full" v-model="form.itemThreshold"
                                            :disabled="!editable" autofocus autocomplete="off" />
                                        <span class="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if= "form.itemThreshold == '' || isNaN(form.itemThreshold) || 
                                                form.itemThreshold <= 1"
                                                v-html="form.itemThreshold == ''
                                                ? 'Invalid: Enter a value'
                                                : isNaN(form.itemThreshold)
                                                ? 'Invalid: Enter a numeric value'
                                                :  form.itemThreshold !== '' && form.itemThreshold < 1
                                                ? 'Invalid: Enter a value greater <br> than or equal to 1'
                                                : ''">
                                        </span>
                                    </div>
                                    <div className="relative w-1/6">
                                        <BreezeLabel for="itemCostPrice" value="Item Cost Price" />
                                        <div className="flex flex-row items-center space-x-2">
                                            <span>
                                                ₱
                                            </span>
                                            <BreezeFloatInput id="itemCostPrice" type="input"
                                                class="mt-1 px-2 py-1 block w-full" v-model="form.itemCostPrice"
                                                :disabled="!editable" autofocus autocomplete="off" />
                                            <span class="error-message text-red-600 text-center text-sm absolute top-full left-0 hidden" v-if= "form.itemCostPrice == '' || isNaN(form.itemCostPrice) || 
                                                form.itemCostPrice <= 1"
                                                v-html="form.itemCostPrice == ''
                                                ? 'Invalid: Enter a value'
                                                : isNaN(form.itemCostPrice)
                                                ? 'Invalid: Enter a numeric value'
                                                :  form.itemCostPrice !== '' && form.itemCostPrice <= 0
                                                ? 'Invalid: Enter a value greater <br> than 0'
                                                : ''">
                                            </span>
                                        </div>
                                    </div>
                                    <div className="relative w-1/6">
                                        <BreezeLabel for="itemSRP" value="Suggested Retail Price" />
                                        <div className="flex flex-row items-center space-x-2">
                                            <span>
                                                ₱
                                            </span>
                                            <BreezeFloatInput id="itemSRP" type="input" class="mt-1 px-2 py-1 block w-full"
                                                v-model="form.itemSRP" :disabled="!editable" autofocus autocomplete="off" />
                                            <span class="error-message text-red-600 text-center text-sm absolute top-full left-0" v-if= "form.itemSRP == '' || isNaN(form.itemSRP) || 
                                                form.itemSRP < ((parseFloat(form.itemCostPrice) * 0.15) + parseFloat(form.itemCostPrice))"
                                                v-html=" form.itemSRP == ''
                                                ? 'Invalid: Enter a value'
                                                : isNaN(form.itemSRP)
                                                ? 'Invalid: Enter a numeric value'
                                                :  form.itemSRP < ((parseFloat(form.itemCostPrice) * 0.15) + parseFloat(form.itemCostPrice))
                                                ? 'Invalid: Enter a value greater <br> than the cost price plus 15%'
                                                : ''">
                                            </span>
                                        </div>
                                    </div>
                                    <div className="w-1/6">
                                        <BreezeLabel for="itemMarkup" value="Item Markup Value" />
                                        <div className="flex flex-row items-center space-x-2">
                                            <BreezeFloatInput id="itemMarkup" type="input"
                                                class="mt-1 px-2 py-1 block w-full" v-model="form.itemMarkup" disabled />
                                            <span>
                                                %
                                            </span>
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-2 w-1/6">
                                        <div>
                                            <BreezeLabel for="isNotifying" value="Low Stock" />
                                        </div>
                                        <div className="flex w-1/12">
                                            <BreezeCheckbox id="isNotifying" type="checkbox" class="mt-1"
                                                v-model="form.isNotifying" :disabled="!editable"
                                                :value="form.isNotifying ? 1 : 0" :checked="form.isNotifying == 1" autofocus
                                                autocomplete="off" />
                                            <span className="text-red-600" v-if="form.errors.isNotifying">
                                                {{ form.errors.isNotifying }}
                                            </span>
                                        </div>
                                        <div>
                                            <BreezeLabel for="isDisabled" value="Disable Item" />
                                        </div>
                                        <div className="flex w-1/12">
                                            <BreezeCheckbox id="isDisabled" type="checkbox" class="mt-1"
                                                v-model="form.isDisabled" :disabled="!editable"
                                                :value="form.isDisabled ? 1 : 0" :checked="form.isDisabled == 1" autofocus
                                                autocomplete="off" />
                                            <span className="text-red-600" v-if="form.errors.isDisabled">
                                                {{ form.errors.isDisabled }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="flex flex-row gap-4 mt-4">
                                <button type="button" v-if="!editable && !showEdit" @click="editable = true"
                                    className="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue">
                                    Edit
                                </button>
                                <button type="submit" v-if="editable"
                                    className="px-6 py-2 text-white font-montserrat bg-dark-pastel-green font-bold rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                    Save
                                </button>
                                <button type="button" v-if="editable"
                                    className="px-6 py-2 text-white font-montserrat bg-persian-red font-bold rounded-md hover:bg-light-coral transition-all duration-300 ease-in-out"
                                    @click="cancelEdit">
                                    Cancel
                                </button>
                                <button type="button" v-if="showEdit" @click="showCurrent"
                                    className="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue">
                                    Go to Current
                                </button>
                                <div v-if="showEdit" class="flex items-right items-center ml-auto">
                                    <div class="flex items-center font-montserrat italic text-xl text-savoy-blue">
                                        Edited On: {{ new Date(showEdit.updated_at).toLocaleDateString() }}, 
                                        {{ new Date(showEdit.updated_at).toLocaleTimeString([], {
                                            hour: 'numeric', minute:
                                                '2-digit'
                                        }) }}
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>

                    <div class="mt-4 flex space-x-4">
                        <div class="w-2/5 noto-sans border-b border-gray-200 rounded-lg overflow-y-auto">
                            <table class="table-auto w-full overflow-hidden rounded-t-md">
                                <thead class="sticky top-0 bg-savoy-blue text-white uppercase">
                                    <tr>
                                        <th class="px-2 py-2 w-1/2 font-montserrat">Date Edited</th>
                                        <th class="px-2 py-2 w-1/2 font-montserrat">Time Edited</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="scrollable-table bg-ghost-white rounded-bl-md">
                                <table class="table-auto w-full overflow-hidden ">
                                    <tbody class="bg-ghost-white">
                                        <tr v-for="log in reversedEditHistory" :key="log.id" @click="handleRowClick(log)"
                                            class="hover:bg-silver">
                                            <template v-if="log.transactionType !== ''">
                                                <td class="border-b px-2 py-2 w-1/2 text-center font-montserrat">{{ new
                                                    Date(log.updated_at).toLocaleDateString() }}</td>
                                                <td class="border-b px-2 py-2 w-1/2 text-center font-montserrat">{{ new
                                                    Date(log.updated_at).toLocaleTimeString([], {
                                                        hour: 'numeric', minute:
                                                            '2-digit'
                                                    }) }}</td>
                                            </template>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="w-3/5 noto-sans border-b border-gray-200 rounded-lg overflow-y-auto">

                            <table class="table-auto w-full overflow-hidden rounded-t-md">
                                <thead class="sticky top-0 bg-savoy-blue text-white uppercase">
                                    <tr>
                                        <th class="px-2 py-2 w-2/6 font-montserrat">
                                            <span class="inline-block pl-2 -ml-2">Quantity History</span>
                                        </th>
                                        <th class="px-2 py-2 w-2/6 font-montserrat">Transaction Date</th>
                                        <th class="px-2 py-2 w-2/6 font-montserrat">Transaction Time</th>
                                    </tr>
                                </thead>
                            </table>


                            <div class="scrollable-table bg-ghost-white rounded-bl-md">
                                <div v-if="reversedQuantityHistory.length === 0"
                                    class="flex items-center justify-center h-full">
                                    <!-- Placeholder content when no row is clicked -->
                                    <div class="text-5xl font-montserrat text-savoy-blue" style="color: #BFBFBF;">
                                        No Quantity Change
                                    </div>
                                </div>
                                <div v-if="reversedQuantityHistory">
                                    <table class="table-auto w-full overflow-hidden ">
                                        <tbody class="bg-ghost-white">
                                            <tr v-for="log in reversedQuantityHistory" :key="log.id">
                                                <template v-if="log.transactionType !== ''">
                                                    <td class="border-b px-2 py-2 w-2/6 text-left font-montserrat">{{
                                                        log.generalNotes }}</td>
                                                    <td class="border-b px-2 py-2 w-2/6 text-center font-montserrat">{{ new
                                                        Date(log.updated_at).toLocaleDateString() }}</td>
                                                    <td class="border-b px-2 py-2 w-2/6 text-center font-montserrat">{{ new
                                                        Date(log.updated_at).toLocaleTimeString([], {
                                                            hour: 'numeric', minute:
                                                                '2-digit'
                                                        }) }}</td>
                                                </template>
                                            </tr>
                                        </tbody>
                                    </table>
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
.error-message {
  display: block;
  position: absolute;
  top: 100%;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
  /* Keep the error message on one line */
}

/* Add these styles for table customization */
table {
    font-family: noto-sans, sans-serif;
    /* Use the defined noto-sans font */
    overflow: hidden;
    /* Hide any overflowing content */
}

/* Add styles for the table headings */
th {
    color: savoy-blue;
    /* Use the color variable savoy-blue */
}

th.uppercase {
    text-transform: uppercase;
    /* Transform text to uppercase */
}

/* Add style for the table body background */
tbody {
    background-color: ghost-white;
    /* Use the color variable ghost-white */
}

/* Add rounded corners to the entire table */
table.rounded-md {
    border-radius: 8px;
    /* Adjust the border-radius as needed */
}

.link-back {
    color: savoy-blue;
    /* Use the color variable savoy-blue */
    font-family: noto-sans, sans-serif;
    /* Use the defined noto-sans font */
}

.scrollable-table {
    height: 340px;
    /* Adjusted height */
    overflow-y: auto;
}

.comments {
    height: 100px;
    resize: none;
}

/* Adjust the style for the sticky header */
thead.sticky tr {
    position: sticky;
    top: 0;
    z-index: 1;
}
.icon {
    color: #BFBFBF;
    /* Default icon color */
}
</style>