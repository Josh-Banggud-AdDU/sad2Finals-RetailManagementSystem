<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import StockInputCard from '@/Components/StockInputCard.vue';
import WarningDuplicateModal from '@/Components/WarningDuplicateModal.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, defineProps, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    inventory_logs: Object,
    inventory_log_items: Array,
    inventories: Array,
    suppliers: Array
})
const form = useForm({
    transactionType: props.inventory_logs.transactionType,
    transactionDate: props.inventory_logs.transactionDate,

    inventory_logID: props.inventory_logs.id,
    itemID: props.inventory_log_items[0].itemID,
    quantity: props.inventory_log_items[0].quantity,
    generalNotes: props.inventory_log_items[0].generalNotes,
});

const supplyOrderItems = ref([]);
const setSupplyOrderItems = () => {
    for (const item of props.inventory_log_items){
        supplyOrderItems.value.push({
            itemID: item.itemID,
            quantity: item.quantity,
            generalNotes: item.generalNotes !== null ? item.generalNotes : "",
            itemName: props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName,
            itemSupplier: props.suppliers.find((supplier) => supplier.id == props.inventories.find((inventory) => inventory.id == item.itemID)?.supplierID).supplierName,
            itemSerialNumber: props.inventories.find((inventory) => inventory.id == item.itemID)?.itemSerialNumber,
        })
    }
}

const transaction = ref("");
onMounted( async() => {
    setSupplyOrderItems();
    transaction.value = form.transactionType;
    console.log(transaction.value);
})

const selectedItem = ref({
    itemSupplier: null,
    itemName: null,
    itemQuantity: null,
    itemID: null,
});

const isVisible = ref(false);
const selectItem = (item) => {
    console.log("Input Card Created");
    isVisible.value = true;
    const selectedSupplier = props.suppliers.find((supplier) => supplier.id === item.supplierID);
    selectedItem.value.itemSupplier = selectedSupplier.supplierName;
    selectedItem.value.itemName = item.itemName;
    selectedItem.value.itemQuantity = item.itemQuantity;
    selectedItem.value.itemID = item.id;
    console.log(selectedItem.value);
};

const isVisible2 = ref(false);
const supplyOrderItemInstanceDupe = ref({});

const receiveSupplyOrderItem = async (supplyOrderItemInstance, itemConfirmed) => {
    const existingItem = ref(supplyOrderItems.value.find((item) => item.itemID == supplyOrderItemInstance.itemID));
    if(!existingItem.value){
        supplyOrderItems.value.push(supplyOrderItemInstance);
        isVisible.value = !itemConfirmed;
        console.log(isVisible.value);
        console.log("Item Pushed to Array");
        console.log(supplyOrderItems.value);
    }else{
        supplyOrderItemInstanceDupe.value = supplyOrderItemInstance
        isVisible2.value = true;
    }
}
const closeInputCard = () => {
    selectedItem.value = {};
    isVisible.value = false;
    console.log("card cancelled");
    console.log(supplyOrderItems.value);
    for (let key in orderItem.value) {
      orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(supplyOrderItems.value);
}

const emit = defineEmits();
const query = ref("");
const itemSuggestions = ref([]);
const supplierSuggestions = ref([]);
const search = async () => {
    if (query.value != "" && runSearch) {
        const response = await fetch(`/search?query=${query.value}`);
        const data = await response.json();

        const uniqueItemNames = new Set();
        itemSuggestions.value = data.inventory.filter((suggestion) => {
            if (!uniqueItemNames.has(suggestion.itemName)) {
                uniqueItemNames.add(suggestion.itemName);
                return true;
            }
            return false;
        });
        const uniqueSupplierNames = new Set();
        supplierSuggestions.value = data.suppliers.filter((suggestion) => {
            if (!uniqueSupplierNames.has(suggestion.supplierName)) {
                uniqueSupplierNames.add(suggestion.supplierName);
                return true;
            }
            return false;
        });

        if (itemSuggestions.value) {
            itemSuggestions.value = await itemSuggestions.value.slice(0, 4);
        }
        if (supplierSuggestions.value) {
            supplierSuggestions.value = await supplierSuggestions.value.slice(0, 4);
        }
    }
}
const selectSuggestion = (suggestion) => {
    runSearch.value = false;
    if (suggestion.hasOwnProperty('supplierName')) {
        query.value = suggestion.supplierName;
        console.log("Supplier Name Found");
    } else if (suggestion.hasOwnProperty('itemName')) {
        query.value = suggestion.itemName;
        console.log("Item Name Found");
    }
    console.log("Query: " + query.value);
    clearSuggestions();
}
const clearSuggestions = () => {
    itemSuggestions.value = [];
    supplierSuggestions.value = [];
}
const runSearch = ref(true);
const resetRunSearch = () => {
    if (query.value) {
        runSearch.value = true;
    }
}
const filteredInventories = computed(() => {
    if (query.value != "") {
        const querySupplier = ref(props.suppliers.filter((supplier) => supplier.supplierName.toLowerCase().includes(query.value.toLowerCase())));
        return props.inventories.filter((item) => {
            return (querySupplier.value.length > 0 && querySupplier.value.some(s => s.id == item.supplierID)) || item.itemName.includes(query.value);
        });
    } else {
        return props.inventories;
    }
});

const orderItem = ref({});
const orderItemIndex = ref(null);
const selectOrderItem = (item, index) => {
    isVisible.value = true;
    orderItem.value = {...item};
    console.log(orderItem.value);
    orderItemIndex.value = index;
    console.log(index + " " + orderItemIndex.value);
    selectedItem.value.itemSupplier = props.suppliers.find((supplier) => supplier.id == props.inventories.find((inventory) => inventory.id == item.itemID)?.supplierID)?.supplierName;
    selectedItem.value.itemName = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName;
    selectedItem.value.itemQuantity = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemQuantity;
    selectedItem.value.itemID = item.itemID;
}

const updateSupplyOrder = (supplyOrderItemInstance, itemConfirmed, index) => {
    supplyOrderItems.value.splice(index, 1, supplyOrderItemInstance);
    isVisible.value = !itemConfirmed;
    console.log(isVisible.value);
    console.log("Item in Array Updated");
    console.log(supplyOrderItems.value);
    for (let key in orderItem.value) {
      orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(supplyOrderItems.value);
}

const continueAddRecord = (supplyOrderItemInstanceDupe) => {
    supplyOrderItems.value.push(supplyOrderItemInstanceDupe);
    isVisible.value = false;
    isVisible2.value = false;
    console.log("Item Pushed to Array");
    console.log(supplyOrderItems.value);
}
const closeDupeWarning = () => {
    isVisible2.value = false;
    closeInputCard();
}
 
const submit = async () => {
    if(supplyOrderItems.value.length > 0){
        //await axios.post(route('inventory_logs_items.update'), { inventory_logID: form.inventory_logID, inventoryLogItems: supplyOrderItems.value });
        //await form.post(route('inventory_logs.update'));
    }
};
</script>

<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                Manage Supply
            </h2>
        </template>
        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <StockInputCard :inventory_logs="props.inventory_logs" :inventories="props.inventories"
                :suppliers="props.suppliers" :selectedItem="selectedItem" :orderItem="orderItem" 
                :orderItemIndex="orderItemIndex" :transaction="transaction" @sendSupplyOrderItem="receiveSupplyOrderItem"
                @close="closeInputCard" @updateSupplyOrderItem="updateSupplyOrder"
                className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>
        <div v-if="isVisible2" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningDuplicateModal :supplyOrderItemInstanceDupe="supplyOrderItemInstanceDupe" @confirmSubmission="continueAddRecord" @close="closeDupeWarning" />
        </div>
        <div class="py-6 h-full ">
            <div class="relative max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
                <div class="mb-4">
                    <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                        <div class="flex items-center justify-between">
                            <Link
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out"
                                :href="route('inventories.manage_supply')">
                            Back
                            </Link>
                        </div>
                    </div>
                </div>
                <div className="flex flex-row gap-4 w-full space-x-2">
                    
                    <div class="w-3/5 h-full overflow-hidden p-4 bg-ghost-white rounded-md">
                        <div class="px-8 pb-2 pt-6 flex justify-between items-center">
                            <div class="w-full flex">
                                <div class="w-3/4 flex flex-col">
                                    <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Supply Order
                                        #{{ props.inventory_logs.id }}</div>
                                    <div class="text-lg font-montserrat font-bold px-1"
                                    :class="{ 'text-dark-pastel-green': form.transactionType === 'Stock Receipt' || form.transactionType === 'Item Return', 'text-persian-red': form.transactionType !== 'Stock Receipt' && form.transactionType !== 'Item Return' }"
                                    >
                                        {{ form.transactionType }}
                                    </div>
                                </div>
                                <div class="w-1/4 flex flex-col pt-1 items-end">
                                    <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">
                                        {{ props.inventory_logs.transactionDate }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="px-6">
                            <hr class="border-t border-solid border-gray-400 my-2">
                        </div>
                        <div>
                            <table class="px-6 table-auto w-full rounded-md">
                                <thead class="sticky top-0">
                                    <tr class="bg-ghost-white text-savoy-blue">
                                        <th class="px-4 py-2 font-montserrat w-2/5">Supplier Name</th>
                                        <th class="px-4 py-2 font-montserrat w-2/5">Item Name</th>
                                        <th class="px-4 py-2 font-montserrat w-1/5">Quantity</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="scrollable-table">
                                <table class="table-auto w-full overflow-hidden rounded-bl-md">
                                    <tbody>
                                        <tr v-for="(supplyOrderItem, index) in supplyOrderItems" :key="supplyOrderItem.id"
                                            @click="selectOrderItem(supplyOrderItem, index)"
                                            class="bg-ghost-white hover:bg-silver transition-all duration-300 ease-in-out"
                                            >
                                            <td class="px-4 py-2 font-montserrat align-top text-left w-2/5">
                                                {{ supplyOrderItem.itemSupplier }}
                                            </td>
                                            <td class="px-4 py-2 font-montserrat text-left w-2/5">
                                                <div>{{ supplyOrderItem.itemName }}</div>
                                                <div class="pt-2 italic text-sm">{{ supplyOrderItem.itemSerialNumber }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 font-montserrat text-center w-1/5">
                                                {{ supplyOrderItem.quantity }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <button type="submit" @click="submit"
                                className="px-6 py-2 text-white font-montserrat bg-dark-pastel-green font-bold rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                Save
                            </button>
                        </div>
                    </div>
                    <div className="flex flex-col gap-2 w-2/5 overflow-hidden h-full">
                        <div class=" relative rounded-md bg-ghost-white">
                            <div className="flex items-center m-1">
                                <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                <BreezeTextInput id="searchInput" type="input"
                                    class="px-2 py-1 block w-full bg-ghost-white border-none drop-shadow-none focus:outline-none"
                                    placeholder="Input Supplier/Item Name" v-model="query" @input="search"
                                    @click="resetRunSearch" autocomplete="off">
                                    <span className="text-red-600" v-if="form.errors.itemName">
                                        {{ form.errors.itemName }}
                                    </span>
                                </BreezeTextInput>
                            </div>
                            <ul v-if="query && (itemSuggestions.length || supplierSuggestions.length)"
                                className="absolute mt-2 w-full bg-ghost-white text-savoy-blue font-bold rounded-lg shadow-lg z-50 p-2">
                                <div class="bg-savoy-blue text-ghost-white">
                                    <span v-if="itemSuggestions.length > 0" className="pl-2 py-2 font-italic">ITEMS:</span>
                                </div>
                                <button type="button" v-for="suggestion in itemSuggestions" :key="suggestion.id"
                                    @click="selectSuggestion(suggestion)" @mouseover="suggestion.hovered = true"
                                    @mouseout="suggestion.hovered = false"
                                    :class="{ 'bg-ghost-white text-vista-blue': !suggestion.hovered, 'bg-silver': suggestion.hovered }"
                                    class="pl-6 text-left w-full">
                                    {{ suggestion.itemName }}
                                </button>

                                <div class="bg-savoy-blue text-ghost-white">
                                    <span v-if="supplierSuggestions.length > 0"
                                        className="pl-2 py-2 font-italic">SUPPLIERS:</span>
                                </div>
                                <button type="button" v-for="suggestion in supplierSuggestions" :key="suggestion.id"
                                    @click="selectSuggestion(suggestion)" @mouseover="suggestion.hovered = true"
                                    @mouseout="suggestion.hovered = false"
                                    :class="{ 'bg-ghost-white text-vista-blue': !suggestion.hovered, 'bg-silver': suggestion.hovered }"
                                    class="pl-6 text-left w-full">
                                    {{ suggestion.supplierName }}
                                </button>

                            </ul>
                        </div>
                        <div className="scrollable overflow-y-auto flex-col flex gap-2">
                            <div v-for="item in filteredInventories" :key="item.id">
                                <button type="button"
                                    className="relative flex flex-col items-center h-24 w-full bg-ghost-white px-8 py-2 sm:rounded-lg hover:bg-silver transition-all duration-300 ease-in-out"
                                    @click="selectItem(item)">
                                    <div className="flex flex-row w-full text-left">
                                        <span className="font-bold text-lg leading-6 line-clamp-2">{{ item.itemName
                                        }}</span>
                                    </div>
                                    <div className="absolute flex pr-8 pl-8 w-full bottom-3">
                                        <span className="italic w-3/6 text-left justify-end">{{ item.itemSerialNumber
                                        }}</span>
                                        <span className="w-1/6"></span>
                                        <span className="italic text-right w-2/6"> {{ item.itemQuantity }} {{ item.itemUnit
                                        }}{{ "." }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>
<style scoped>
.bg-black {
    background-color: black;
    position: fixed;
    z-index: 40;
    /* Ensure the dimmed background is behind the modal (z-index 50) */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.opacity-50 {
    opacity: 0.5;
}

.icon {
    color: #BFBFBF;
    /* Default icon color */
}

#searchInput::placeholder {
    font-style: italic;
    font-size: 0.9rem;
}

.scrollable-table {
    /* Adjusted height */
    overflow-y: auto;
    height: 600px;
}

.scrollable {
    /* Adjusted height */
    overflow-y: auto;
    height: 740px;
}

/* Adjust the style for the sticky header */
thead.sticky tr {
    position: sticky;
    top: 0;
    z-index: 1;
}</style>