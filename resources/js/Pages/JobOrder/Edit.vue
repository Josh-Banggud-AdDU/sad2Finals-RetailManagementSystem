<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import JobServiceInputCard from '@/Components/JobServiceInputCard.vue';
import JobItemInputCard from '@/Components/JobItemInputCard.vue';
import WarningDuplicateModal from '@/Components/WarningDuplicateModal.vue';
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import JobOrderPaymentModal from '@/Components/JobOrderPayment.vue';
import { ref, computed, onMounted, onUnmounted, defineEmits, reactive } from 'vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const props = defineProps({
    jobOrders: Object,
    jobOrderItems: Array,
    jobOrderServices: Array,
    jobOrderEmployees: Array,
    inventories: Array,
    services: Array,
    employees: Array,
})

const firstJobOrderItem = props.jobOrderItems[0] ?? {};

const form = useForm({
    jobOrderStatus: props.jobOrders.jobOrderStatus,
    vehicleDetails: props.jobOrders.vehicleDetails,
    generalNotes: props.jobOrders.generalNotes,
    total: props.jobOrders.total,
    netChange: 0,
    payment: 0,

    jobOrderID: props.jobOrders.id,

    itemID: firstJobOrderItem.itemID,
    quantityUsed: firstJobOrderItem.quantityUsed,
    itemAmountPaid: firstJobOrderItem.amountPaid,

    serviceID: props.jobOrderServices[0].serviceID,
    serviceAmountPaid: props.jobOrderServices[0].amountPaid,

    employeeID: props.jobOrderEmployees[0].employeeID
});

const selectedItem = ref({
    itemSRP: null,
    itemName: null,
    itemQuantity: null,
    itemID: null,
    itemSerialNumber: null,
    itemThreshold: null,
});
const selectedService = ref({
    serviceName: null,
    serviceID: null,
})
const selectedEmployee = ref({
    lastName: null,
    firstName: null,
    employeeID: null,
})

const jobOrderItems = reactive([]);
const jobOrderServices = reactive([]);
const jobOrderEmployees = reactive([]);
const setJOItems = () => {
    jobOrderItems.value = [];
    for(const item of props.jobOrderItems){
        jobOrderItems.value.push({
            itemID: item.itemID,
            quantityUsed: item.quantityUsed,
            amountPaid: item.amountPaid,
            itemName: props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName,
            itemSerialNumber: props.inventories.find((inventory) => inventory.id == item.itemID)?.itemSerialNumber,
            itemSRP: props.inventories.find((inventory) => inventory.id == item.itemID)?.itemSRP,
        })
    }
}
const setJOServices = () => {
    jobOrderServices.value = [];
    for(const service of props.jobOrderServices){
        jobOrderServices.value.push({
            serviceID: service.serviceID,
            amountPaid: service.amountPaid,
            serviceName: props.services.find((record) => record.id == service.serviceID)?.serviceName,
        })
    }
}
const setJOEmployees = () => {
    jobOrderEmployees.value = [];
    for(const employee of props.jobOrderEmployees){
        jobOrderEmployees.value.push({
            employeeID: employee.employeeID,
            firstName: props.employees.find((record) => record.id == employee.employeeID)?.firstName,
            lastName: props.employees.find((record) => record.id == employee.employeeID)?.lastName,
        })
    }
}

const statusRef = ref(null);
const itemSearchRef = ref(null);
const serviceSearchRef = ref(null);
onMounted(async () => {
    setJOItems();
    setJOEmployees();
    setJOServices();
    calculateTotal();
    const handleClickOutsideStatus = (event) => {
    if (statusRef.value && !statusRef.value.contains(event.target)) {
        isVisible6.value = false;
        }
    };
    const handleClickOutsideSearch = (event) => {
    if ((serviceSearchRef.value && !serviceSearchRef.value.contains(event.target)) || 
        (itemSearchRef.value && !itemSearchRef.value.contains(event.target))) {
        clearSuggestions();
        }
    };

    document.addEventListener('mousedown', handleClickOutsideStatus);
    document.addEventListener('mousedown', handleClickOutsideSearch);

    onUnmounted(() => {
        document.removeEventListener('mousedown', handleClickOutsideStatus);
        document.removeEventListener('mousedown', handleClickOutsideSearch);
    });
})

const isVisible = ref(false);
const selectItem = (item) => {
    console.log("Input Card Created");
    isVisible.value = true;
    selectedItem.value.itemSRP = item.itemSRP;
    selectedItem.value.itemName = item.itemName;
    selectedItem.value.itemQuantity = item.itemQuantity;
    selectedItem.value.itemID = item.id;
    selectedItem.value.itemSerialNumber = item.itemSerialNumber;
    selectedItem.value.itemThreshold = item.itemThreshold;
    console.log(selectedItem.value);
};
const selectService = (service) => {
    console.log("Service Card Created");
    isVisible.value = true;
    selectedService.value.serviceName = service.serviceName;
    selectedService.value.serviceID = service.id;
    console.log(selectedService.value);
}
const selectEmployee = (employee) => {
    console.log("Employee Card Created");
    isVisible.value = true;
    selectedEmployee.value.lastName = employee.lastName;
    selectedEmployee.value.firstName = employee.firstName;
    selectedEmployee.value.employeeID = employee.id;
    const jobOrderEmployee = {...selectedEmployee.value};
    receiveJobOrderEmployee(jobOrderEmployee);
}

const existingItem = ref(false);
const receiveJobOrderItem = async (jobOrderItemInstance, itemConfirmed) => {
    console.log(jobOrderItemInstance);
    if(jobOrderItems.value != undefined){
        existingItem.value = !!jobOrderItems.value.find((item) => item.itemID == jobOrderItemInstance.itemID);
    }
    if(!existingItem.value) {
        isVisible.value = !itemConfirmed;
        console.log("Item Pushed to Array");
        if(jobOrderItems.value == undefined){
            jobOrderItems.value = [];
            jobOrderItems.value.push(jobOrderItemInstance);
        }else{
            jobOrderItems.value.push(jobOrderItemInstance);
        }
        calculateTotal();
    } else if(existingItem.value){
        isVisible2.value = true;
    }
    console.log(jobOrderItems.value);
}

const existingService = ref(false);
const receiveJobOrderService = async (jobOrderServiceInstance, serviceConfirmed) => {
    console.log(jobOrderServiceInstance);
    if(jobOrderServices.value != undefined){
        existingService.value = !!jobOrderServices.value.find((service) => service.serviceID == jobOrderServiceInstance.serviceID);
    }
    if(!existingService.value) {
        isVisible.value = !serviceConfirmed;
        console.log("Service Pushed to Array");
        if(jobOrderServices.value == undefined){
            jobOrderServices.value = [];
            jobOrderServices.value.push(jobOrderServiceInstance);
        }else{
            jobOrderServices.value.push(jobOrderServiceInstance);
        }
        calculateTotal();
    } else if(existingService.value){
        isVisible2.value = true;
    }
    console.log(jobOrderServices.value);
}

const existingEmployee = ref(false);
const receiveJobOrderEmployee = async (jobOrderEmployeeInstance) => {
    console.log(jobOrderEmployeeInstance);
    if(jobOrderEmployees.value != undefined){
        existingEmployee.value = !!jobOrderEmployees.value.find((employee) => employee.employeeID == jobOrderEmployeeInstance.employeeID);
        console.log("Employee Exists: ", existingEmployee.value);
    }
    if(!existingEmployee.value) {
        console.log("Service Pushed to Array");
        if(jobOrderEmployees.value == undefined){
            jobOrderEmployees.value = [];
            jobOrderEmployees.value.push(jobOrderEmployeeInstance);
        }else{
            jobOrderEmployees.value.push(jobOrderEmployeeInstance);
        }
    } else if(existingEmployee.value){
        isVisible2.value = true;
    }
    console.log(jobOrderEmployees.value);
}
const totalTotalSales = ref(0);
const calculateTotal = () => {
    console.log('Calculating Total');
    totalTotalSales.value = 0;
    if(form.jobOrderStatus != 'Cancelled'){
        if(jobOrderItems.value != undefined){
            for(const item of jobOrderItems.value){
                totalTotalSales.value += parseFloat(item.amountPaid);
            }
        }
        if(jobOrderServices.value != undefined){
            for(const service of jobOrderServices.value){
                totalTotalSales.value += parseFloat(service.amountPaid);
            }
        }
    }else{
        if(jobOrderItems.value != undefined){
            for(const item of jobOrderItems.value){
                totalTotalSales.value += parseFloat(item.amountPaid);
            }
        }
    }
    console.log('Total: ', totalTotalSales.value);
    form.netChange = (totalTotalSales.value - form.total).toFixed(2);
}

const closeInputCard = () => {
    selectedItem.value = {};
    isVisible.value = false;
    console.log("card cancelled");
    console.log(jobOrderItems.value);
    for (let key in orderItem.value) {
        orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(jobOrderItems.value);
}

const emit = defineEmits();
const searchItem = ref("");
const searchService = ref("");
const searchEmployee = ref("");
const itemSuggestions = ref([]); 
const serviceSuggestions = ref([]);

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
const searchServices = async () => {
    if(searchService.value != "" && runServicesSearch.value == true){
        const response = await fetch(`/search_services?searchService=${searchService.value}`);
        const data = await response.json();

        const uniqueServiceNames = new Set();
        serviceSuggestions.value = data.filter((suggestion) => {
            if(!uniqueServiceNames.has(suggestion.serviceName)){
                uniqueServiceNames.add(suggestion.serviceName);
                return true;
            }
            return false;
        });

        if(serviceSuggestions.value){
            serviceSuggestions.value = await serviceSuggestions.value.slice(0, 6);
        }
    }
}

const selectItemSuggestion = (itemSuggestion) => {
    runInventorySearch.value = false;
    searchItem.value = itemSuggestion.itemName;
    clearSuggestions();
}
const selectServiceSuggestion = (serviceSuggestion) => {
    runServicesSearch.value = false;
    searchService.value = serviceSuggestion.serviceName;
    clearSuggestions();
}

const clearSuggestions = async () => {
    itemSuggestions.value = [];
    serviceSuggestions.value = [];
}

const runInventorySearch = ref(true);
const runServicesSearch = ref(true);
const resetRunSearch = () => {
    if (searchItem.value) {
        runInventorySearch.value = true;
    }
    if (searchService.value){
        runServicesSearch.value = true;
    }
}

const filter = ref(2);
const filteredInventories = computed(() => {
    if (searchItem.value != "") {
        return props.inventories.filter((item) => item.itemName.toLowerCase().includes(searchItem.value.toLowerCase()));
    } else {
        console.log("Input field has no content");
        return props.inventories;
    }
});
const filteredServices = computed(() => {
    if (searchService.value != "") {
        return props.services.filter((service) => service.serviceName.toLowerCase().includes(searchService.value.toLowerCase()));
    } else {
        console.log("Input field has no content");
        return props.services;
    }
});
const filteredEmployees = computed(() => {
    if (searchEmployee.value != "") {
        return props.employees.filter((employee) => {
            return(employee.firstName.toLowerCase().includes(searchEmployee.value.toLowerCase()) ||
                employee.lastName.toLowerCase().includes(searchEmployee.value.toLowerCase())
            )
        });
    } else {
        console.log("Input field has no content");
        return props.employees;
    }
});
const chooseFilter = (event) => {
    isVisible.value = false;
    if (event.currentTarget.id == 'service') {
        filter.value = 1;
    } else if (event.currentTarget.id == 'employee') {
        filter.value = 2;
    } else if (event.currentTarget.id == 'item') {
        filter.value = 3;
    }
    console.log(event.currentTarget.id, filter.value);
}
const remove = (element, index) => {
    console.log("Removing: ",index,  element);
    if(element.hasOwnProperty('serviceID')){
        jobOrderServices.value.splice(index, 1);
        console.log(jobOrderServices.value);
    }else if(element.hasOwnProperty('employeeID')){
        jobOrderEmployees.value.splice(index, 1);
        console.log(jobOrderEmployees.value);
    }else if(element.hasOwnProperty('itemID')){
        jobOrderItems.value.splice(index, 1);
        console.log(jobOrderItems.value);
    }
    calculateTotal();
}

const orderItem = ref({});
const orderItemIndex = ref(null);
const selectOrderItem = (item, index) => {
    filter.value = 3;
    isVisible.value = true;
    orderItem.value = { ...item };
    orderItemIndex.value = index;
    console.log(orderItem.value);
    console.log(index + " " + orderItemIndex.value);
    selectedItem.value.itemSRP = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemSRP;
    selectedItem.value.itemName = item.itemName;
    selectedItem.value.itemQuantity = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemQuantity;
    selectedItem.value.itemID = item.itemID;
    selectedItem.value.itemSerialNumber = item.itemSerialNumber;
    selectedItem.value.itemThreshold = props.inventories.find((inventory) => inventory.id == item.itemID)?.itemThreshold;
}
const orderService = ref({});
const orderServiceIndex = ref(null);
const selectOrderService = (service, index) => {
    filter.value = 1;
    isVisible.value = true;
    orderService.value = {...service};
    orderServiceIndex.value = index;
    console.log(orderService.value);
    selectedService.value.serviceName = service.serviceName;
    selectedService.value.serviceID = service.serviceID;
}

const updateJobOrderItem = (jobOrderItemInstance, itemConfirmed, index) => {
    jobOrderItems.value.splice(index, 1, jobOrderItemInstance);
    isVisible.value = !itemConfirmed;
    console.log("Item in Array Updated");
    console.log(jobOrderItems.value);
    calculateTotal();
    for (let key in orderItem.value) {
      orderItem.value[key] = null;
    }
    console.log("Order Item inside: " + JSON.stringify(orderItem.value, null, 2));
    console.log(orderItemIndex.value);
    console.log(jobOrderItems.value);
}
const updateJobOrderService = (jobOrderServiceInstance, serviceConfirmed, index) => {
    jobOrderServices.value.splice(index, 1, jobOrderServiceInstance);
    isVisible.value = !serviceConfirmed;
    console.log("Service in Array Updated");
    console.log(jobOrderServices.value);
    calculateTotal();
    for (let key in orderService.value) {
      orderService.value[key] = null;
    }
    console.log("Order Service inside: " + JSON.stringify(orderService.value, null, 2));
    console.log(orderServiceIndex.value);
    console.log(jobOrderServices.value);
}

const isVisible2 = ref(false);
const closeWarning = () => {
    isVisible2.value = false;
    isVisible3.value = false;
    isVisible4.value = false;
    isVisible5.value = false;
    isVisible7.value = false;
    closeInputCard();
}

const formatServiceID = (service) => {
    return String(service).padStart(5, '0');
};

const status = ref([]);
const isVisible6 = ref(false);
const showStatusDropdown = () => {
    if(form.jobOrderStatus == 'Ongoing'){
        status.value[0] = {status: 'Done'};
        status.value[1] = {status: 'Cancelled'};
    }else if(form.jobOrderStatus == 'Cancelled'){
        status.value[0] = {status: 'Ongoing'};
        status.value[1] = {status: 'Done'};
    }else if(form.jobOrderStatus == 'Done'){
        status.value[0] = {status: 'Ongoing'};
        status.value[1] = {status: 'Cancelled'};
    }
    isVisible6.value = true;
}
const selectStatus = (status) => {
    form.jobOrderStatus = status;
    isVisible6.value = false;
    calculateTotal();
}

const checkVehicleDetails = () => {
    if(form.vehicleDetails == ''){
        form.errors.vehicleDetails = "Invalid: Enter vehicle details";
    }else{
        form.errors.vehicleDetails = "";
    }
}

const isVisible5 = ref(false);
const exit = () => {
    Inertia.visit(route('job_orders.index'));
}

const isVisible7 = ref(false);
const paymentInputted = ref(false);
const confirm = async () => {
    isVisible4.value = false;
    console.log("Order Submitted to Database ", jobOrderItems.value, jobOrderServices.value, jobOrderEmployees.value);
    
    //Check if cancelled, if yes
    if((form.jobOrderStatus == "Cancelled" || form.jobOrderStatus == "Done") && paymentInputted.value == false){
        isVisible7.value = true;
    }else{
    //Check if partially paid/fully paid, if yes, check if a service/item is removed
    /*if(props.jobOrders.total > totalTotalSales.value && parseFloat(props.jobOrders.customerBalance) + parseFloat(form.netChange) < 0){
        console.log(props.jobOrders.total, totalTotalSales.value);
        isVisible7.value = true;
    }else{*/
        axios.post(route('job_order_items.update'), {
            jobOrderID: form.jobOrderID,
            ...(jobOrderItems.value && jobOrderItems.value.length > 0 && { jobOrderItems: jobOrderItems.value })
        });
        axios.post(route('job_order_services.update'), {
            jobOrderID: form.jobOrderID,
            jobOrderServices: jobOrderServices.value,
        });
        axios.post(route('job_order_employees.update'), {
            jobOrderID: form.jobOrderID,
            jobOrderEmployees: jobOrderEmployees.value,
        })
        form.post(route('job_orders.update'), {
            jobOrderID: form.jobOrderID,
            vehicleDetails: form.vehicleDetails,
            generalNotes: form.generalNotes,
            jobOrderStatus: form.jobOrderStatus,
            netChange: form.netChange,
            payment: form.payment
        });
    }
}

const receivePayment = (payment) => {
    form.payment = payment;
    paymentInputted.value = true;
    console.log(form.payment);
    closeInputCard();
    confirm();
}

const isVisible3 = ref(false);
const isVisible4 = ref(false);
const submit = async () => {
    if (jobOrderServices.value == undefined || jobOrderEmployees.value == undefined || form.vehicleDetails == "" || 
        jobOrderServices.value.length == 0 || jobOrderEmployees.value.length == 0) {
        checkVehicleDetails();
        isVisible3.value = true;
    }else if(jobOrderServices.value.length > 0 && jobOrderEmployees.value.length > 0){
        isVisible4.value = true;
    }
};
</script>

<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                Edit Job Order
            </h2>
        </template>
        <div v-if="isVisible && filter === 1" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <JobServiceInputCard :jobOrders="props.jobOrders" :services="props.services" :selectedService="selectedService"
                :orderService="orderService" :orderServiceIndex="orderServiceIndex" @sendJobOrderService="receiveJobOrderService"
                @close="closeInputCard" @updateJobOrderService="updateJobOrderService" className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
        </div>
        <div v-if="isVisible && filter === 3" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <JobItemInputCard :jobOrders="props.jobOrders" :inventories="props.inventories" :selectedItem="selectedItem"
                :orderItem="orderItem" :orderItemIndex="orderItemIndex" @sendJobOrderItem="receiveJobOrderItem"
                @close="closeInputCard" @updateJobOrderItem="updateJobOrderItem" className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50" />
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
        <div v-if="isVisible7" class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black opacity-50"></div>
            <JobOrderPaymentModal :jobOrders="props.jobOrders" :balance="props.jobOrders.amountDue"
                :newTotal="totalTotalSales" @sendPayment="receivePayment"
                @close="closeWarning" className="absolute border-b border-gray-200 shadow-sm rounded-lg z-50"/>
        </div>
        <div class="py-6 h-full ">
            <div class="relative max-w-7xl mx-auto sm:px-6 lg:px-8 h-full">
                <div class="mb-4">
                    <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                        <div class="flex items-center justify-between">
                            <button 
                                @click="isVisible5 = true"
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out">
                                Back
                            </button>
                            <!--<Link
                                class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue hover:bg-vista-blue transition-all duration-300 ease-in-out"
                                :href="route('job_orders.index')">
                            Back
                            </Link>-->
                        </div>
                    </div>
                </div>
                <div className="flex flex-row gap-4 w-full space-x-2">
                    <div class="w-3/5 h-full overflow-hidden p-4 bg-ghost-white rounded-md">
                        <div class="px-8 pb-2 pt-6 flex flex-col justify-between items-center">
                            <div class="w-full flex">
                                <div class="w-3/4 flex flex-col">
                                    <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">
                                        Job Order #{{ props.jobOrders.id }}
                                    </div>
                                    <div class="flex flex-row gap-4 items-center text-lg font-montserrat font-bold px-1" :class="{
                                        'text-saffron': form.jobOrderStatus == 'Ongoing',
                                        'text-persian-red': form.jobOrderStatus == 'Cancelled',
                                        'text-dark-pastel-green': form.jobOrderStatus == 'Done',
                                    }">
                                        Status:
                                        <div className="relative flex flex-row gap-2 w-36" ref="statusRef">
                                            <button type="button" @click="showStatusDropdown" 
                                                class="self-center h-full transition-all transform active:-rotate-180 focus:-rotate-180 duration-300 ease-in-out">
                                                <div class="w-5 h-5" :class="{
                                                    'icon-container-3': form.jobOrderStatus == 'Ongoing',
                                                    'icon-container-2': form.jobOrderStatus == 'Cancelled',
                                                    'icon-container-4': form.jobOrderStatus == 'Done',
                                                }">
                                                    <font-awesome-icon :icon="['fas', 'caret-down']" class="icon-3 w-3 h-3" />
                                                </div>
                                            </button>
                                            <div className="self-center">{{ form.jobOrderStatus }}</div>
                                            <ul v-if="isVisible6"
                                            className="absolute mt-8 w-full bg-ghost-white text-savoy-blue text-sm font-bold rounded-lg shadow-lg z-50 p-2">
                                                <button type="button" v-for="choice in status" :key="choice.id"
                                                    @click="selectStatus(choice.status)"
                                                    @mouseover="choice.hovered = true" @mouseout="choice.hovered = false"
                                                    :class="{ 'bg-ghost-white text-savoy-blue': !choice.hovered, 'bg-silver': choice.hovered }"
                                                    class="pl-2 text-left w-full">
                                                    {{ choice.status }}
                                                </button>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="w-1/4 flex flex-col pt-1 items-end">
                                    <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">
                                        {{ props.jobOrders.jobOrderDate }}</div>
                                    <div class="text-lg text-right italic font-montserrat font-bold px-1" :class="{
                                    'text-persian-red': props.jobOrders.paymentStatus == 'Unpaid',
                                    'text-saffron': props.jobOrders.paymentStatus == 'Partially Paid',
                                    'text-dark-pastel-green': props.jobOrders.paymentStatus == 'Fully Paid',
                                    }">
                                        {{ props.jobOrders.paymentStatus }}
                                    </div>
                                </div>
                            </div>
                            <div class="w-full relative font-montserrat px-1">
                                <BreezeTextArea id="vehicleDetails" type="input" @input="checkVehicleDetails"
                                    className="mt-1 px-2 py-1 rounded-md w-full comments-2 text-sm" placeholder="Vehicle Model, License Plate, Color, etc."
                                    v-model="form.vehicleDetails" autofocus autocomplete="off"/>
                                <span className="error-message text-red-600 text-sm absolute top-full left-0 -mt-1" v-if="form.errors.vehicleDetails">
                                    {{ form.errors.vehicleDetails }}
                                </span>
                            </div>
                        </div>
                        <div class="px-6">
                            <hr class="border-t border-solid border-gray-400 my-2">
                        </div>
                        <div className="scrollable-table">
                            <div>
                                <table class="px-6 table-auto w-full rounded-md">
                                    <thead class="sticky top-0">
                                        <!-- First row of headers -->
                                        <tr class="bg-ghost-white text-savoy-blue">
                                            <th class="px-6 py-2 font-montserrat w-full">Mechanic Name</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div>
                                    <table class="table-auto w-full font-montserrat overflow-hidden rounded-md">
                                        <tbody>
                                            <tr v-for="(jobOrderEmployee, index) in jobOrderEmployees.value" :key="jobOrderEmployee.id"
                                                class="bg-ghost-white flex items-center">
                                                <button type="button" @click.stop="remove(jobOrderEmployee, index)" class="h-full pl-6">
                                                    <div class="icon-container-2 w-7 h-7">
                                                        <font-awesome-icon :icon="['fas', 'trash-can']" class="icon-3 w-4 h-4" />
                                                    </div>
                                                </button>
                                                <td class="px-4 py-2 font-montserrat text-left w-full">
                                                    <div>{{ jobOrderEmployee.firstName }} {{ jobOrderEmployee.lastName }}</div>
                                                    <div class="pt-2 italic text-sm">{{ props.employees.find((employee) => employee.id == jobOrderEmployee.employeeID)?.number }}</div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                                            <th class="px-6 py-2 font-montserrat w-4/6">Service Name</th>
                                            <th class="px-6 py-2 font-montserrat w-2/6">Subtotal</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="">
                                    <table class="table-auto w-full font-montserrat overflow-hidden rounded-md">
                                        <tbody>
                                            <tr v-for="(jobOrderService, index) in jobOrderServices.value" :key="jobOrderService.id"
                                                @click="selectOrderService(jobOrderService, index)"
                                                class="bg-ghost-white flex items-center hover:bg-silver transition-all duration-300 ease-in-out">
                                                <button type="button" @click.stop="remove(jobOrderService, index)" class="h-full pl-6">
                                                    <div class="icon-container-2 w-7 h-7">
                                                        <font-awesome-icon :icon="['fas', 'trash-can']" class="icon-3 w-4 h-4" />
                                                    </div>
                                                </button>
                                                <td class="px-4 py-2 font-montserrat text-left w-4/6">
                                                    <div>{{ jobOrderService.serviceName }}</div>
                                                    <div class="pt-2 italic text-sm">{{ formatServiceID(jobOrderService.serviceID) }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-2 -ml-10 font-montserrat text-right w-2/6"
                                                    :class="{'line-through': form.jobOrderStatus == 'Cancelled'}">
                                                    {{ parseFloat(jobOrderService.amountPaid).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
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
                                            <th class="px-6 py-2 font-montserrat w-3/6">Item Name</th>
                                            <th class="px-6 py-2 font-montserrat w-1/6">Quantity</th>
                                            <th class="px-6 py-2 font-montserrat w-2/6">Subtotal</th>
                                        </tr>
                                    </thead>
                                </table>
                                <div class="">
                                    <table class="table-auto w-full font-montserrat overflow-hidden rounded-md">
                                        <tbody>
                                            <tr v-for="(jobOrderItem, index) in jobOrderItems.value" :key="jobOrderItem.id"
                                                @click="selectOrderItem(jobOrderItem, index)"
                                                class="bg-ghost-white flex items-center hover:bg-silver transition-all duration-300 ease-in-out">
                                                <button type="button" @click.stop="remove(jobOrderItem, index)" class="h-full pl-6">
                                                    <div class="icon-container-2 w-7 h-7">
                                                        <font-awesome-icon :icon="['fas', 'trash-can']" class="icon-3 w-4 h-4" />
                                                    </div>
                                                </button>
                                                <td class="px-4 py-2 font-montserrat text-left w-3/6">
                                                    <div>{{ jobOrderItem.itemName }}</div>
                                                    <div class="pt-2 italic text-sm">{{ jobOrderItem.itemSerialNumber }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-2 -ml-12 font-montserrat text-center w-1/6">
                                                    {{ jobOrderItem.quantityUsed }} {{ props.inventories.find((item) => item.id == jobOrderItem.itemID)?.itemUnit }}.
                                                </td>
                                                <td class="px-6 py-2 font-montserrat text-right w-2/6">
                                                    {{ parseFloat(jobOrderItem.amountPaid).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
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
                            <div className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                                <div>Total:</div>
                                <span>{{ parseFloat(totalTotalSales ? totalTotalSales : 0).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}</span> 
                            </div>
                        </div>
                    </div>
                    <div className="flex flex-col gap-2 w-2/5 overflow-hidden h-full">
                        <div className="flex flex-row gap-2">
                            <button type="button" id="employee" @click="chooseFilter($event)">
                                <div class="icon-container w-10 h-10">
                                    <font-awesome-icon :icon="['fas', 'users']" class="icon-2 w-7 h-7" />
                                </div>
                            </button>
                            <button type="button" id="service" @click="chooseFilter($event)">
                                <div class="icon-container w-10 h-10">
                                    <font-awesome-icon :icon="['fas', 'motorcycle']" class="icon-2 w-7 h-7" />
                                </div>
                            </button>
                            <button type="button" id="item" @click="chooseFilter($event)">
                                <div class="icon-container w-10 h-10">
                                    <font-awesome-icon :icon="['fas', 'box-open']" class="icon-2 w-7 h-7" />
                                </div>
                            </button>
                            <div className="flex flex-col grow">
                                <div v-if="filter == 2" class="relative rounded-md bg-silver">
                                    <div className="flex items-center m-1">
                                        <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon-4 w-4 h-4 p-2" />
                                        <BreezeTextInput id="searchInput" type="input"
                                            class="px-2 py-1 block w-full bg-silver border-none focus:outline-none"
                                            v-model="searchEmployee" :disabled="true" autocomplete="off">
                                        </BreezeTextInput>
                                    </div>
                                </div>
                                <div v-if="filter == 1" class="relative rounded-md bg-ghost-white" ref="serviceSearchRef">
                                    <div className="flex items-center m-1">
                                        <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                        <BreezeTextInput id="searchInput" type="input"
                                            class="px-2 py-1 block w-full bg-ghost-white border-none focus:outline-none"
                                            placeholder="Input Service Name" v-model="searchService" @input="searchServices"
                                            @click="resetRunSearch" autocomplete="off">
                                        </BreezeTextInput>
                                    </div>
                                    <ul v-if="searchService && serviceSuggestions.length"
                                        className="absolute mt-2 w-full bg-ghost-white text-savoy-blue font-bold rounded-lg shadow-lg z-50 p-2">
                                        <button type="button" v-for="suggestion in serviceSuggestions" :key="suggestion.id"
                                            @click="selectServiceSuggestion(suggestion)" @mouseover="suggestion.hovered = true"
                                            @mouseout="suggestion.hovered = false"
                                            :class="{ 'bg-ghost-white text-savoy-blue': !suggestion.hovered, 'bg-silver': suggestion.hovered }"
                                            class="pl-6 text-left w-full">
                                            {{ suggestion.serviceName }}
                                        </button>
                                    </ul>
                                </div>
                                <div v-if="filter == 3" class="relative rounded-md bg-ghost-white" ref="itemSearchRef">
                                    <div className="flex items-center m-1">
                                        <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                                        <BreezeTextInput id="searchInput" type="input"
                                            class="px-2 py-1 block w-full bg-ghost-white border-none focus:outline-none"
                                            placeholder="Input Item Name" v-model="searchItem" @input="searchInventory"
                                            @click="resetRunSearch" autocomplete="off">
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
                            </div>
                        </div>
                        <div v-if="filter === 1" className="scrollable overflow-y-auto flex-col flex gap-2">
                            <div v-for="service in filteredServices" :key="service.id">
                                <button type="button"
                                    className="relative flex flex-col figtree items-center h-18 w-full bg-ghost-white px-8 py-2 sm:rounded-lg hover:bg-silver transition-all duration-300 ease-in-out"
                                    @click="selectService(service)">
                                    <div className="flex flex-row w-full text-left">
                                        <span className="font-bold text-lg leading-6 line-clamp-2">{{ service.serviceName
                                        }}</span>
                                    </div>
                                    <div className="flex w-full">
                                        <span className="italic text-left justify-end">{{ formatServiceID(service.id) }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div v-if="filter === 2" className="scrollable overflow-y-auto flex-col flex gap-2">
                            <div v-for="employee in filteredEmployees" :key="employee.id">
                                <button type="button"
                                    className="relative flex flex-col figtree items-center h-18 w-full bg-ghost-white px-8 py-2 sm:rounded-lg hover:bg-silver transition-all duration-300 ease-in-out"
                                    @click="selectEmployee(employee)">
                                    <div className="flex flex-row w-full text-left">
                                        <span className="font-bold text-lg leading-6 line-clamp-2">{{ employee.firstName }} {{ employee.lastName }}</span>
                                    </div>
                                    <div className="flex w-full">
                                        <span className="italic text-left justify-end">{{ employee.position }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                        <div v-if="filter === 3" className="scrollable overflow-y-auto flex-col flex gap-2">
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

.icon-container {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #f7f8ff;
    /* Default background color */
    border-radius: 20%;
    /* Creates a circular shape */
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
.icon-container-3 {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #F1C749;
    /* Default background color */
    border-radius: 100%;
    /* Creates a circular shape */
}
.icon-container-4 {
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: #51C451;
    /* Default background color */
    border-radius: 100%;
    /* Creates a circular shape */
}

.icon-2 {
    color: #516AC4;
    /* Default icon color */
}

.icon-2:hover {
    color: #7991E6;
  }

.icon-3 {
    color: #F7F8FF;
    /* Default icon color */
}

.icon-4 {
    color: #959595;
    /* Default icon color */
}

#searchInput::placeholder {
    font-style: italic;
    font-size: 0.9rem;
}

.scrollable-table {
    /* Adjusted height */
    overflow-y: auto;
    height: 440px;
}

.scrollable {
    /* Adjusted height */
    overflow-y: auto;
    height: 740px;
}

.comments {
    height: 75px;
    resize: none;
}

.comments-2{
    height: 50px;
    resize: none;
}

/* Adjust the style for the sticky header */
thead.sticky tr {
    position: sticky;
    top: 0;
    z-index: 1;
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