<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import SalesInputCard from '@/Components/SalesInputCard.vue';
import WarningDuplicateModal from '@/Components/WarningDuplicateModal.vue';
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import { ref, computed, onMounted, onUnmounted, defineEmits, reactive } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const props = defineProps({
    sales: Array,
    inventories: Array,
})
const form = useForm({
    salesID: props.sales.id,
    itemID: '',
    quantitySold: '',
    amountPaid: '',
    generalNotes: ''
});

const selectedItem = ref({
    itemSRP: null,
    itemName: null,
    itemQuantity: null,
    itemID: null,
    itemSerialNumber: null,
    itemThreshold: null
});

const itemSearchRef = ref(null);
onMounted(() => {
    console.log(props.inventories);
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
    console.log(item.id + " " + item.itemName + " " + item.itemQuantity);
    isVisible.value = true;
    selectedItem.value.itemSRP = item.itemSRP;
    selectedItem.value.itemName = item.itemName;
    selectedItem.value.itemQuantity = item.itemQuantity;
    selectedItem.value.itemID = item.id;
    selectedItem.value.itemSerialNumber = item.itemSerialNumber;
    selectedItem.value.itemThreshold = item.itemThreshold;
    console.log(selectedItem.value);
};

const isVisible2 = ref(false);
const existingItem = ref(false);
const salesOrderItems = reactive([]);
const receiveSalesOrderItem = (salesOrderItemInstance, itemConfirmed) => {
    console.log(salesOrderItemInstance);
    if (salesOrderItems.value != undefined) {
        existingItem.value = !!salesOrderItems.value.find((item) => item.itemID == salesOrderItemInstance.itemID);
    }
    if (!existingItem.value) {
        console.log("Item Pushed to Array");
        isVisible.value = !itemConfirmed;
        if (salesOrderItems.value == undefined) {
            salesOrderItems.value = [];
            salesOrderItems.value.push(salesOrderItemInstance);
        } else {
            salesOrderItems.value.push(salesOrderItemInstance);
        }
        calculateTotal();
    } else if (existingItem.value) {
        isVisible2.value = true;
    }
    console.log(salesOrderItems.value);
}
const totalTotalSales = ref(0);
const calculateTotal = () => {
    console.log('Calculating Total');
    totalTotalSales.value = 0;
    for (const item of salesOrderItems.value) {
        totalTotalSales.value += parseFloat(item.amountPaid);
        console.log('Total: ', totalTotalSales.value, " ", item.amountPaid);
    }
}

const closeInputCard = () => {
    selectedItem.value = {};
    isVisible.value = false;
    console.log("card cancelled");
    console.log(salesOrderItems.value);
    for (let key in orderItem.value) {
        orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(salesOrderItems.value);
}

const emit = defineEmits();
const searchItem = ref("");
const itemSuggestions = ref([]);
const searchInventory = async () => {
    if (searchItem.value != "" && runInventorySearch.value == true) {
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
    runInventorySearch.value = false;
    searchItem.value = itemSuggestion.itemName;
    clearItemSuggestions();
}
const clearItemSuggestions = () => {
    itemSuggestions.value = [];
}
const runInventorySearch = ref(true);
const resetRunSearch = () => {
    if (searchItem.value) {
        runInventorySearch.value = true;
    }
}
const filteredInventories = computed(() => {
    if (searchItem.value != "") {
        return props.inventories.filter((item) => item.itemName.toLowerCase().includes(searchItem.value.toLowerCase()));
    } else {
        console.log("Input field has no content");
        return props.inventories;
    }
});

const remove = (element, index) => {
    console.log("Removing: ",index,  element);
    salesOrderItems.value.splice(index,1);
    console.log(salesOrderItems.value);
    calculateTotal();
}

const orderItem = ref({});
const orderItemIndex = ref(null);
const selectOrderItem = (item, index) => {
    isVisible.value = true;
    orderItem.value = { ...item };
    orderItemIndex.value = index;
    console.log(index + " " + orderItemIndex.value);
    selectedItem.value.itemSRP = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemSRP;
    selectedItem.value.itemName = item.itemName;
    selectedItem.value.itemQuantity = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemQuantity;
    selectedItem.value.itemID = item.itemID;
    selectedItem.value.itemSerialNumber = item.itemSerialNumber;
    selectedItem.value.itemThreshold = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemThreshold;
}

const updateSalesOrder = (salesOrderItemInstance, itemConfirmed, index) => {
    salesOrderItems.value.splice(index, 1, salesOrderItemInstance);
    isVisible.value = !itemConfirmed;
    console.log("Item in Array Updated");
    console.log(salesOrderItems.value);
    calculateTotal();
    for (let key in orderItem.value) {
        orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(salesOrderItems.value);
}

const closeWarning = () => {
    isVisible2.value = false;
    isVisible3.value = false;
    isVisible4.value = false;
    isVisible5.value = false;
    closeInputCard();
}

const isVisible5 = ref(false);
const exit = () => {
    Inertia.visit(route('sales.index'));
}
const confirm = async () => {
    isVisible4.value = false;
    console.log("Order Submitted to Database", salesOrderItems);
    axios.post(route('sales_items.store'), {
        salesID: form.salesID,
        salesItems: salesOrderItems.value
    });
    form.post(route('sales.store'), {
        salesID: form.salesID,
        generalNotes: form.generalNotes
    });
}

const isVisible3 = ref(false);
const isVisible4 = ref(false);
const submit = () => {
    if(salesOrderItems.value == undefined || salesOrderItems.value.length == 0){
        isVisible3.value = true;
    }else if (salesOrderItems.value.length > 0){
        isVisible4.value = true;
    }
};
</script>

<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                Create Sales Order
            </h2>
        </template>
        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <SalesInputCard :sales="props.sales" :inventories="props.inventories" :selectedItem="selectedItem"
                :orderItem="orderItem" :orderItemIndex="orderItemIndex" @sendSalesOrderItem="receiveSalesOrderItem"
                @close="closeInputCard" @updateSalesOrderItem="updateSalesOrder"
                className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>
        <div v-if="isVisible2" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningDuplicateModal @close="closeWarning" />
        </div>
        <div v-if="isVisible3" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningIncompleteModal @close="closeWarning" />
        </div>
        <div v-if="isVisible4" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmInputModal @confirmSubmission="confirm" @close="closeWarning" />
        </div>
        <div v-if="isVisible5" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmBackModal @confirmSubmission="exit" @close="closeWarning" />
        </div>
        <div class="py-6 h-full ">
            <div class="relative max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
                <div class="mb-4">
                    <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                        <div class="flex items-center justify-between">
                            <button type="button"
                                @click="isVisible5 = true"
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out">
                                Back
                            </button>
                            <!--<Link
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out"
                                :href="route('sales.index')">
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
                                    <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Sales Order
                                        #{{ props.sales.id }}</div>
                                </div>
                                <div class="w-1/4 flex flex-col pt-1 items-end">
                                    <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">
                                        {{ props.sales.salesDate }}</div>
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
                                        <th class="pl-11"></th>
                                        <th class="px-6 py-2 font-montserrat w-5/12">Item Name</th>
                                        <th class="px-6 py-2 font-montserrat w-3/12">Quantity</th>
                                        <th class="px-6 py-2 font-montserrat w-4/12">Subtotal</th>
                                    </tr>
                                </thead>
                            </table>
                            <div class="scrollable-table">
                                <table class="table-auto w-full pt-2 font-montserrat overflow-hidden rounded-md">
                                    <tbody>
                                        <tr v-for="(salesOrderItem, index) in salesOrderItems.value"
                                            :key="salesOrderItem.id" @click="selectOrderItem(salesOrderItem, index)"
                                            class="bg-ghost-white flex items-center hover:bg-silver transition-all duration-300 ease-in-out">
                                            <button type="button" @click.stop="remove(salesOrderItem, index)" class="pl-4">
                                                <div class="icon-container-2 w-7 h-7 transition-all duration-300 ease-in-out">
                                                    <font-awesome-icon :icon="['fas', 'trash-can']" class="flex items-center icon-3 w-4 h-4" />
                                                </div>
                                            </button>
                                            <td class="px-6 py-2 font-montserrat text-left w-5/12">
                                                <div>{{ salesOrderItem.itemName }}</div>
                                                <div class="pt-2 italic text-sm">{{ salesOrderItem.itemSerialNumber }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-2 font-montserrat text-center w-3/12">
                                                {{ salesOrderItem.quantitySold }} {{ props.inventories.find((item) =>
                                                    item.id == salesOrderItem.itemID)?.itemUnit }}.
                                            </td>
                                            <td class="px-8 py-2 font-montserrat text-right w-4/12">
                                                {{ parseFloat(salesOrderItem.amountPaid).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="px-6 m-2">
                            <BreezeTextArea id="generalNotes" type="input"
                                className="mt-1 px-2 py-1 rounded-md w-full comments" placeholder="Order Notes"
                                v-model="form.generalNotes" />
                        </div>
                        <div className="flex flex-row justify-between">
                            <button type="submit" @click="submit"
                                className="px-6 py-2 text-white font-montserrat bg-dark-pastel-green font-bold rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                Save
                            </button>
                            <div
                                className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                                <div>Total:</div>
                                <span>{{ parseFloat(totalTotalSales ? totalTotalSales : 0).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="flex flex-col gap-2 w-2/5 overflow-hidden h-full">
                        <div class=" relative rounded-md bg-ghost-white" ref="itemSearchRef">
                            <div className="flex items-center m-1">
                                <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                <BreezeTextInput id="searchInput" type="input"
                                    class="px-2 py-1 block w-full bg-ghost-white border-none drop-shadow-none focus:outline-none"
                                    placeholder="Input Item Name" v-model="searchItem" @input="searchInventory"
                                    @click="resetRunSearch" autocomplete="off">
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
                                    :class="{ 'bg-ghost-white text-savoy-blue': !suggestion.hovered, 'bg-silver': suggestion.hovered }"
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
    width: 1rem; /* Adjust the width to your preference */
    height: 1rem; /* Adjust the height to your preference */
    padding-bottom: 1px;
    padding-left: 0.5px;
}

.button-container {
    display: flex;
    align-items: center;
}

.icon-container-2 {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #C33A3A;
    /* Default background color */
    border-radius: 50%;
    /* Creates a circular shape */
}

.icon-container-2:hover {
    background-color: #DC8288;
}


#searchInput::placeholder {
    font-style: italic;
    font-size: 0.9rem;
}

.scrollable-table {
    /* Adjusted height */
    overflow-y: auto;
    height: 465px;
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