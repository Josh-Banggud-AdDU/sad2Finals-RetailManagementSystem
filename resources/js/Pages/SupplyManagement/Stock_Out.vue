<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import StockInputCard from '@/Components/StockInputCard.vue';
import WarningDuplicateModal from '@/Components/WarningDuplicateModal.vue';
import StockInformationModal from '@/Components/StockInformationModal.vue';
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, onMounted, onUnmounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const props = defineProps({
    inventory_logs: Object,
    inventories: Array,
    suppliers: Array,
    employees: Array,
    all_inventory_logs: Array,
})
const form = useForm({
    transactionType: "Stock Out",
    employeeID: '',
    supplierID: '',
    referenceNum: '',
    generalNotes: '',
    inventory_logID: props.inventory_logs.id,
    itemID: '',
    quantity: '',
    generalNotes: '',
    supplierName: ''
});

const selectedItem = ref({
    itemName: null,
    itemQuantity: null,
    itemID: null,
});

const itemSearchRef = ref(null);
onMounted(() => {
    const handleClickOutsideSearch = (event) => {
    if (itemSearchRef.value && !itemSearchRef.value.contains(event.target)) {
        clearItemSuggestions();
        }
    };

    document.addEventListener('mousedown', handleClickOutsideSearch);

    onUnmounted(() => {
        document.removeEventListener('mousedown', handleClickOutsideSearch);
    });
})

const isVisible = ref(false);
const selectItem = (item) => {
    console.log("Input Card Created");
    isVisible.value = true;
    selectedItem.value.itemName = item.itemName;
    selectedItem.value.itemQuantity = item.itemQuantity;
    selectedItem.value.itemID = item.id;
    console.log(selectedItem.value);
};

const isVisible2 = ref(false);
const existingItem = ref(false);
const supplyOrderItems = reactive([]);
const receiveSupplyOrderItem = (supplyOrderItemInstance, itemConfirmed) => {
    if(supplyOrderItems.value != undefined){
        existingItem.value = !!supplyOrderItems.value.find((item) => item.itemID == supplyOrderItemInstance.itemID);
    }
    console.log(existingItem.value);
    if(!existingItem.value){
        console.log("Item Pushed to Array");
        isVisible.value = !itemConfirmed;
        if(supplyOrderItems.value == undefined){
            supplyOrderItems.value = [];
            supplyOrderItems.value.push(supplyOrderItemInstance);
        }else{
            supplyOrderItems.value.push(supplyOrderItemInstance);
        }
        console.log(supplyOrderItems.value);
    }else if(existingItem.value){
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

const transaction = ref(form.transactionType);

const emit = defineEmits();
const searchItem = ref("");
const itemSuggestions = ref([]);
const searchInventory = async () => {
  if (searchItem.value != "") {
    const response = await fetch(`/search_inventories?searchItem=${searchItem.value}`);
    const data = await response.json();

    const uniqueItemNames = new Set();
    itemSuggestions.value = data.filter((suggestion) => {
      if (!uniqueItemNames.has(suggestion.itemName)) {
        uniqueItemNames.add(suggestion.itemName);
        return true;
      }
      return false;
    });

    if (itemSuggestions.value) {
        itemSuggestions.value = await itemSuggestions.value.slice(0, 6);
    }
  }
}
const selectItemSuggestion = (itemSuggestion) => {
  searchItem.value = itemSuggestion.itemName;
  clearItemSuggestions();
}
const clearItemSuggestions = () => {
    console.log("Clearing suggestions")
  itemSuggestions.value = [];
}
const filteredInventories = computed(() => {
    if (searchItem.value != "") {
        console.log(props.inventories.filter((item) => item.itemName.toLowerCase().includes(searchItem.value.toLowerCase())));
        return props.inventories.filter((item) => item.itemName.toLowerCase().includes(searchItem.value.toLowerCase()));
    } else {
        console.log(props.inventories);
        return props.inventories;
    }
});

const remove = (element, index) => {
    console.log("Removing: ", index, element);
    supplyOrderItems.value.splice(index,1);
    console.log(supplyOrderItems.value);
}

const orderItem = ref({});
const orderItemIndex = ref(null);
const selectOrderItem = (item, index) => {
    isVisible.value = true;
    orderItem.value = {...item};
    orderItemIndex.value = index;
    console.log(index + " " + orderItemIndex.value);
    selectedItem.value.itemName = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName;
    selectedItem.value.itemQuantity = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemQuantity;
    selectedItem.value.itemID = item.itemID;
}

const updateSupplyOrder = (supplyOrderItemInstance, itemConfirmed, index) => {
    supplyOrderItems.value.splice(index, 1, supplyOrderItemInstance);
    isVisible.value = !itemConfirmed;
    console.log("Item in Array Updated");
    console.log(supplyOrderItems.value);
    for (let key in orderItem.value) {
      orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(supplyOrderItems.value);
}

const closeWarning = () => {
    isVisible2.value = false;
    isVisible4.value = false;
    isVisible5.value = false;
    isVisible6.value = false;
    closeInputCard();
}

const isVisible3 = ref(true);
const recordStockIn = (employee, refNum, supplier) => {
    console.log(props.inventories);
    form.employeeID = employee;
    form.referenceNum = refNum;
    isVisible3.value = false;
}

const isVisible6 = ref(false);
const exit = () => {
    Inertia.visit(route('inventories.manage_supply'));
}
const confirm = () => {
    isVisible5.value = false;
    console.log("Log Submitted to Database ", supplyOrderItems.value);
    axios.post(route('inventory_logs_items.store'), {
        inventory_logID: form.inventory_logID,
        inventoryLogItems: supplyOrderItems.value
    }).catch(error => {
        console.error('Error in inventory_logs_items.store request:', error);
    });
    form.post(route('inventory_logs.store'), {
        inventory_logID: form.inventory_logID,
        employeeID: form.employeeID,
        supplierID: form.supplierID,
        referenceNum: form.referenceNum,
        generalNotes: form.generalNotes,
        salesID: null
    });
}

const isVisible4 = ref(false);
const isVisible5 = ref(false);
const submit = () => {
    if(supplyOrderItems.value == undefined || supplyOrderItems.value.length == 0){
        isVisible4.value = true;
    }else if(supplyOrderItems.value.length > 0){
        isVisible5.value = true;
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
        <div v-if="isVisible3" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <StockInformationModal v-if="isVisible3" :employees="props.employees" :logs="props.all_inventory_logs" :transaction="form.transactionType" @confirmSubmission="recordStockIn" 
            className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>

        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <StockInputCard :inventory_logs="props.inventory_logs" :inventories="props.inventories"
                :suppliers="props.suppliers" :selectedItem="selectedItem" :orderItem="orderItem" 
                :orderItemIndex="orderItemIndex" :transaction="transaction" @sendSupplyOrderItem="receiveSupplyOrderItem"
                @close="closeInputCard" @updateSupplyOrderItem="updateSupplyOrder"
                className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>
        <div v-if="isVisible2" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningDuplicateModal @close="closeWarning"/>
        </div>
        <div v-if="isVisible4" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningIncompleteModal @close="closeWarning" />
        </div>
        <div v-if="isVisible5" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmInputModal @confirmSubmission="confirm" @close="closeWarning" />
        </div>
        <div v-if="isVisible6" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmBackModal @confirmSubmission="exit" @close="closeWarning" />
        </div>
        <div v-if="!isVisible3" class="py-6 h-full ">
            <div class="relative max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
                <div class="mb-4">
                    <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                        <div class="flex items-center justify-between">
                            <button type="button"
                                @click="isVisible6 = true"
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out">
                                Back
                            </button>
                            <!--<Link
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out"
                                :href="route('inventories.manage_supply')">
                            Back
                            </Link>-->
                        </div>
                    </div>
                </div>
                <div className="flex flex-row gap-4 w-full space-x-2">
                    <div class="w-3/5 h-full overflow-hidden p-4 bg-ghost-white rounded-md">
                        <div class="px-8 pb-2 pt-6 flex justify-between items-center">
                            <div class="w-full flex">
                                <div class="w-3/4 flex flex-col">
                                    <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">
                                        Supply Log #{{ props.inventory_logs.id }}</div>
                                        <div class="text-lg font-montserrat text-savoy-blue font-bold px-1">
                                        Reference Number: {{ form.referenceNum }}
                                    </div>
                                    <div class="text-sm font-montserrat text-persian-red font-bold px-1">
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
                                    <!-- First row of headers -->
                                    <tr class="bg-ghost-white text-savoy-blue">
                                        <th class="px-6 py-2 font-montserrat w-4/5">Item Name</th>
                                        <th class="px-6 py-2 font-montserrat w-1/5">Quantity</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="scrollable-table">
                                <table class="table-auto w-full font-montserrat overflow-hidden rounded-md">
                                    <tbody>
                                        <tr v-for="(supplyOrderItem, index) in supplyOrderItems.value" :key="supplyOrderItem.id"
                                            @click="selectOrderItem(supplyOrderItem, index)"
                                            class="bg-ghost-white flex items-center hover:bg-silver transition-all duration-300 ease-in-out"
                                            >
                                            <button type="button" @click.stop="remove(supplyOrderItem, index)" class="h-full pl-6">
                                                <div class="icon-container-2 w-7 h-7">
                                                    <font-awesome-icon :icon="['fas', 'trash-can']" class="icon-3 w-4 h-4" />
                                                </div>
                                            </button>
                                            <td class="px-4 py-2 font-montserrat text-left w-4/5">
                                                <div>{{ supplyOrderItem.itemName }}</div>
                                                <div class="pt-2 italic text-sm">{{ supplyOrderItem.itemSerialNumber }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-2 font-montserrat text-center w-1/5">
                                                {{ supplyOrderItem.quantity }} {{ props.inventories.find((item) => item.id == supplyOrderItem.itemID)?.itemUnit }}.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="px-6 m-2">
                            <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments" placeholder="Order Notes" v-model="form.generalNotes"/>
                        </div>
                        <div>
                            <button type="submit" @click="submit"
                                className="px-6 py-2 text-white font-montserrat bg-dark-pastel-green font-bold rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                Save
                            </button>
                        </div>
                    </div>
                    <div className="flex flex-col gap-2 w-2/5 overflow-hidden h-full">
                        <div class=" relative rounded-md bg-ghost-white" ref="itemSearchRef">
                            <div className="flex items-center m-1">
                                <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                <BreezeTextInput id="searchInput" type="input"
                                    class="px-2 py-1 block w-full bg-ghost-white border-none drop-shadow-none focus:outline-none"
                                    placeholder="Input Item Name" v-model="searchItem" @input="searchInventory"
                                    autocomplete="off">
                                    <span className="text-red-600" v-if="form.errors.itemName">
                                        {{ form.errors.itemName }}
                                    </span>
                                </BreezeTextInput>
                            </div>
                            <ul v-if="searchItem && itemSuggestions.length"
                                className="absolute mt-2 w-full bg-ghost-white text-savoy-blue font-bold rounded-lg shadow-lg z-50 p-2">
                                <button type="button" v-for="suggestion in itemSuggestions" :key="suggestion.id"
                                    @click="selectItemSuggestion(suggestion)" @mouseover="suggestion.hovered = true"
                                    @mouseout="suggestion.hovered = false"
                                    :class="{ 'bg-ghost-white text-vista-blue': !suggestion.hovered, 'bg-silver': suggestion.hovered }"
                                    class="pl-6 text-left w-full">
                                    {{ suggestion.itemName }}
                                </button>
                            </ul>
                        </div>
                        <div className="scrollable overflow-y-auto flex-col flex gap-2">
                            <div v-for="item in filteredInventories" :key="item.id">
                                <button type="button"
                                    className="relative flex flex-col figtree items-center h-24 w-full bg-ghost-white px-8 py-2 sm:rounded-lg hover:bg-silver transition-all duration-300 ease-in-out"
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

.icon-3 {
    color: #F7F8FF;
    /* Default icon color */
}
.icon-container-2 {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #C33A3A;
    /* Default background color */
    border-radius: 100%;
    /* Creates a circular shape */
}

#searchInput::placeholder {
    font-style: italic;
    font-size: 0.9rem;
}

.scrollable-table {
    /* Adjusted height */
    overflow-y: auto;
    height: 415px;
}

.scrollable {
    /* Adjusted height */
    overflow-y: auto;
    height: 740px;
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
}</style>