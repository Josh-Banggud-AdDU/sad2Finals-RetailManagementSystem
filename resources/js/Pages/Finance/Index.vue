<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue'
import { Head, Link, useForm, defineProps } from '@inertiajs/inertia-vue3';
import { ref, watch, reactive, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  finances: Array,
  sales: Array,
  sale_items: Array,
  job_orders: Array,
  job_order_items: Array,
  job_order_employees: Array, //remove
  job_order_services: Array, //remove here and controller
  cashflows: Array,
  cash_on_hands: Array,
  inventories: Array,
  cash_on_hand_histories: Array
})

const form = useForm({
  thousands: '',
  fiveHundreds: '',
  twoHundreds: '',
  oneHundreds: '',
  fifties: '',
  twenties: '',
  tens: '',
  fives: '',
  ones: '',
  twentyFiveCentavos: '',
  tenCentavos: '',
  fiveCentavos: '',
  oneCentavos: ''
});

const reversedFinances = computed(() => {
  const filteredFinances = props.finances.filter((finance) => {
    const financeDate = new Date(finance.created_at);
    const financeYear = financeDate.getFullYear();
    const financeMonth = financeDate.getMonth() + 1;

    //console.log(financeMonth, financeYear, selectedYear.value ,selectedMonth.value);
    return financeYear == selectedYear.value && financeMonth == months[selectedMonth.value - 1].value;
  })
  return [...filteredFinances].reverse();
});

const salesItems = ref([]);
const jobOrderItems = ref([]);
const cashflow = ref([]);
const selectedLogDetails = ref({});
//const jobOrderServices = ref([]);
//const jobOrderEmployees = ref([]);
const handleRowClick = (log) => {
  selectedLog.value = log;
  console.log(selectedLog.value);
  if (selectedLog.value.salesID) {
    salesItems.value = props.sale_items.filter((item) => item.salesID == selectedLog.value.salesID);
    selectedLogDetails.value = props.sales.find((log) => log.id == selectedLog.value.salesID);
    console.log(selectedLogDetails.value)
  } else if (selectedLog.value.jobOrderID) {
    jobOrderItems.value = props.job_order_items.filter((item) => item.jobOrderID == selectedLog.value.jobOrderID);
    selectedLogDetails.value = props.job_orders.find((log) => log.id == selectedLog.value.jobOrderID);
  } else if(selectedLog.value.cashflowID){
    //cashflow.value = props.cashflows.filter((log) => log.cashflowID == selectedLog.value.cashflowID);
    selectedLogDetails.value = props.cashflows.find((log) => log.id == selectedLog.value.cashflowID);
  }
  //supplyOrderItems.value = props.inventory_log_items.filter((item) => item.inventory_logID === selectedLog.value.id);
};

const selectedLog = ref(null);

const netCashflow = computed(() => {
  return reversedFinances.value.reduce((sum, finance) => {
    if (finance.transactionType !== '') {
      return sum + finance.amount;
    }
    return sum;
  }, 0);
});

const months = [
  { value: 1, label: 'January' },
  { value: 2, label: 'February' },
  { value: 3, label: 'March' },
  { value: 4, label: 'April' },
  { value: 5, label: 'May' },
  { value: 6, label: 'June' },
  { value: 7, label: 'July' },
  { value: 8, label: 'August' },
  { value: 9, label: 'September' },
  { value: 10, label: 'October' },
  { value: 11, label: 'November' },
  { value: 12, label: 'December' },
];

const currentYear = new Date().getFullYear();
const selectedMonth = ref(new Date().getMonth() + 1); // Default to the current month
const selectedMonthValue = ref(months[selectedMonth.value - 1].label);
const selectedYear = ref(currentYear); // Default to the current year

const monthRef = ref(null);
onMounted(() => {
  //selectedMonth.value = months[selectedMonth.value - 1].label;
  //console.log(selectedMonth.value);

  const handleClickOutsideMonth = (event) => {
    if (monthRef.value && !monthRef.value.contains(event.target)) {
      displayMonth.value = false;
    }
  }
  document.addEventListener('mousedown', handleClickOutsideMonth);
  onUnmounted(() => {
    document.removeEventListener('mousedown', handleClickOutsideMonth);
  });
})

const maxAllowedYear = computed(() => currentYear);

/*
const selectedMonthName = computed(() => {
  // You can create an array of month names and use it to display the selected month
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
  ];
  return monthNames[selectedMonth.value - 1];
});*/

const displayMonth = ref(false);
const selectMonth = (month) => {
  selectedMonth.value = month.value;
  selectedMonthValue.value = months[selectedMonth.value - 1].label;
  displayMonth.value = false;
}
const add = () => {
  if (selectedYear.value < maxAllowedYear.value) {
    selectedYear.value++;
  }
}
const subtract = () => {
  if (selectedYear.value > 0) {
    selectedYear.value--;
  }
}

// Add this function to calculate the total based on the provided formula
function calculateTotalCashOnHand(row) {
  return (
    row.thousands * 1000 +
    row.fiveHundreds * 500 +
    row.twoHundreds * 200 +
    row.oneHundreds * 100 +
    row.fifties * 50 +
    row.twenties * 20 +
    row.tens * 10 +
    row.fives * 5 +
    row.ones * 1 +
    row.twentyFiveCentavos * 0.25 +
    row.tenCentavos * 0.10 +
    row.fiveCentavos * 0.05 +
    row.oneCentavos * 0.01
  );
}

// Modify the computed property for netCashOnHand
const netCashOnHand = computed(() => {
  const selectedMonthYear = new Date(selectedYear.value, selectedMonth.value - 1);

  // Filter Cash on Hand records for the selected month and year
  const cashOnHandForMonth = props.cash_on_hands.filter((cashOnHand) => {
    const cashOnHandDate = new Date(cashOnHand.created_at);
    return (
      cashOnHandDate.getMonth() === selectedMonthYear.getMonth() &&
      cashOnHandDate.getFullYear() === selectedMonthYear.getFullYear()
    );
  });

  // Calculate the total cash on hand for the selected month
  const totalCashOnHand = cashOnHandForMonth.reduce((total, cashOnHandRow) => {
    return total + calculateTotalCashOnHand(cashOnHandRow);
  }, 0);

  return totalCashOnHand;
});

// Modify the computed property for netCashOnHandHistories
const netCashOnHandHistories = computed(() => {
  const selectedMonthYear = new Date(selectedYear.value, selectedMonth.value - 1);

  // Filter Cash on Hand Histories for the selected month and year
  const cashOnHandHistoriesForMonth = props.cash_on_hand_histories.filter((history) => {
    const historyDate = new Date(history.created_at);
    return (
      historyDate.getMonth() === selectedMonthYear.getMonth() &&
      historyDate.getFullYear() === selectedMonthYear.getFullYear()
    );
  });

  // Calculate the total cash on hand histories for the selected month
  const totalCashOnHandHistories = cashOnHandHistoriesForMonth.reduce((total, history) => {
    return total + calculateTotalCashOnHand(history);
  }, 0);

  return totalCashOnHandHistories;
});

// Add this new computed property for netIncreaseInCashOnHand
const netIncreaseInCashOnHand = computed(() => {
  // Calculate the difference between netCashOnHand and netCashOnHandHistoriess
  return netCashOnHand.value - netCashOnHandHistories.value;

});

const discrepancies = computed(() => {
  return netIncreaseInCashOnHand.value - netCashflow.value;
});


</script>
<template>
  <Head title="Dashboard" />
  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
        Manage Finances
      </h2>
    </template>
    <div class="py-4 w-full h-full flex flex-col gap-4">
      <div class="w-full flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
          <div className="flex items-center justify-between">
            <Link
              class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
              :href="route('finances.main')">
            Back
            </Link>
          </div>
        </div>
      </div>
      <div class="w-full flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="w-full flex items-center gap-4">
          <Link
            class="flex justify-center w-11/12 px-6 py-2 text-white bg-dark-pastel-green text-3xl font-black rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
            :href="route('cashflows.create')">
          +
          </Link>

          <Link
            class="flex justify-center w-1/12 px-6 py-2 text-white font-montserrat bg-dark-pastel-green text-lg font-bold rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
            :href="route('finances.print')">
          <font-awesome-icon id="print" :icon="['fas', 'print']" class="icon w-5 h-5 p-2" />
          </Link>
        </div>
      </div>
      <div class="w-full flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8 grow">
        <div class="h-32 flex flex-row gap-4 w-full grow">
          <!-- Existing table -->
          <div class="flex flex-col space-y-4 w-2/5">
            <div class="flex flex-row w-full bg-savoy-blue rounded-lg">
              <div class="flex flex-row items-center py-1 px-2 bg-savoy-blue rounded-lg shadow-sm" ref="monthRef">
                <div class="relative w-full">
                  <BreezeTextInput id="month" type="input" v-model="selectedMonthValue"
                    class="my-1 px-2 py-1 block w-full rounded-md figtree" @click="displayMonth = true" autofocus
                    readonly />
                  <!--v-model="month.label" @focus="searchEmployee" @blur="displayInvalid"-->
                  <ul v-if="displayMonth"
                    className=" absolute mt-2 w-full bg-ghost-white max-h-96 overflow-y-auto text-savoy-blue font-bold border border-gray-300 rounded-lg shadow-lg z-40 p-2">
                    <button id="monthButton" type="button" v-for="month in months" :key="month.value" :value="month.value"
                      @click="selectMonth(month)" className="text-left w-full pl-2 hover:bg-silver">
                      <!-- @blur="displayInvalid"-->
                      {{ month.label }}
                    </button>
                  </ul>
                </div>
                <font-awesome-icon id="dropdownIcon" :icon="['fas', 'caret-down']"
                  class="icon w-5 h-5 p-2 transform transition duration-300" :class="{
                    '-rotate-180': displayMonth,
                    '-rotate-0': !displayMonth
                  }" />
              </div>
              <div class="flex flex-row justify-between items-center py-1 px-2 bg-savoy-blue rounded-lg shadow-sm grow">
                <button type="button" @click.stop="subtract" class="h-full px-2">
                  <font-awesome-icon :icon="['fas', 'minus']" class="icon w-4 h-4" />
                </button>
                <BreezeTextInput id="year" type="input" v-model="selectedYear"
                  class="my-1 px-2 py-1 block w-full rounded-md figtree text-center" readonly />
                <button type="button" @click.stop="add" class="h-full px-2">
                  <font-awesome-icon :icon="['fas', 'plus']" class="icon w-4 h-4" />
                </button>
              </div>
            </div>
            <div class="flex flex-col w-full h-full bg-ghost-white rounded-md">
              <table class="table-fixed w-full overflow-hidden rounded-t-md">
                <thead class="sticky top-0 bg-savoy-blue text-white uppercase">
                  <tr>
                    <th class="py-2 w-6/12 font-montserrat" colspan="2">Date & Time</th>
                    <th class="py-2 w-3/12 font-montserrat">Type</th>
                    <th class="px-2 py-2 w-3/12 font-montserrat">Amount</th>
                  </tr>
                </thead>
              </table>
              <div class="h-32 overflow-y-auto rounded-md grow">
                <table class="table-fixed w-full overflow-hidden">
                  <tbody class="bg-ghost-white min-h-full">
                    <tr v-for="log in reversedFinances" :key="log.id" @click="handleRowClick(log)"
                      class="hover:bg-silver">
                      <template v-if="log.transactionType !== ''">
                        <td class="border-b pl-6 py-2 w-3/12 text-center font-montserrat">{{ new
                          Date(log.updated_at).toLocaleDateString() }}</td>
                        <td class="border-b pr-6 py-2 w-3/12 text-center font-montserrat">{{ new
                          Date(log.updated_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }) }}</td>
                        <td class="border-b px-2 py-2 w-3/12 text-left font-montserrat">{{ log.transactionType }}</td>
                        <td class="border-b pr-4 py-2 w-3/12 text-right font-montserrat">{{
                          parseFloat(log.amount).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}</td>
                      </template>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- New table -->
          <div class="h-full w-3/5 grow">
            <div class="flex flex-col bg-ghost-white rounded-md p-4 min-h-full max-h-full">
              <div v-if="selectedLog === null" class="flex items-center justify-center grow">
                <!-- Placeholder content when no row is clicked -->
                <div class="text-5xl font-montserrat text-savoy-blue" style="color: #BFBFBF;">
                  Transaction Preview
                </div>
              </div>
              <!--Preview for JO-->
              <div v-if="selectedLog && selectedLog.jobOrderID" class="flex flex-col grow min-h-full max-h-full">
                <div class="px-8 pb-2 pt-6 flex flex-col justify-between items-center">
                  <div class="w-full flex">
                    <div class="w-3/4 flex flex-col">
                      <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Job Order #{{
                        selectedLogDetails.id }}
                      </div>
                      <div class="text-lg font-montserrat font-bold px-1" :class="{
                        'text-saffron': selectedLogDetails.jobOrderStatus == 'Ongoing',
                        'text-persian-red': selectedLogDetails.jobOrderStatus == 'Cancelled',
                        'text-dark-pastel-green': selectedLogDetails.jobOrderStatus == 'Done',
                      }">
                        Status: {{ selectedLogDetails.jobOrderStatus }}
                      </div>
                    </div>
                    <div class="w-1/4 flex flex-col pt-1 items-end">
                      <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">{{
                        selectedLogDetails.jobOrderDate }}</div>
                      <div class="text-lg text-right italic font-montserrat font-bold px-1" :class="{
                        'text-persian-red': selectedLogDetails.paymentStatus == 'Unpaid',
                        'text-dark-pastel-green': selectedLogDetails.paymentStatus == 'Fully Paid',
                      }">
                        {{ selectedLogDetails.paymentStatus }}
                      </div>
                    </div>
                  </div>
                  <div class="w-full font-montserrat px-1">
                    <BreezeTextArea id="vehicleDetails" type="input"
                      className="mt-1 px-2 py-1 rounded-md w-full comments-2 text-sm"
                      placeholder="Vehicle Model, License Plate, Color, etc." v-model="selectedLogDetails.vehicleDetails"
                      :disabled="true" />
                  </div>
                </div>
                <div class="px-6">
                  <hr class="border-t border-solid border-gray-400 my-2">
                </div>
                <div class="min-h-full max-h-full grow flex flex-col gap-2 justify-between">
                  <div class="h-32 overflow-y-auto grow flex flex-col">
                    <table class="table-fixed w-full rounded-md">
                      <thead>
                        <!-- First row of headers -->
                        <tr class="bg-ghost-white text-savoy-blue">
                          <th class="px-6 py-2 font-montserrat w-3/6">Item Name</th>
                          <th class="px-6 py-2 font-montserrat w-1/6">Quantity</th>
                          <th class="px-6 py-2 font-montserrat w-2/6">Subtotal</th>
                        </tr>
                      </thead>
                      <tbody class="bg-ghost-white max-h-500 overflow-y-auto">
                        <!-- Content row with the same formatting as headers -->
                        <tr v-if="selectedLog !== null" v-for="item in jobOrderItems"
                          class="bg-ghost-white text-savoy-blue">
                          <td class="px-6 py-2 font-montserrat text-gray-800 w-3/6">
                            <div>{{ props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName }}</div>
                            <div class="pt-2 italic text-sm">{{ props.inventories.find((inventory) => inventory.id ==
                              item.itemID)?.itemSerialNumber }}</div>
                          </td>
                          <td class="px-4 py-2 font-montserrat text-gray-800 w-1/6 text-center">{{ item.quantityUsed }} {{
                            props.inventories.find((inventory) => inventory.id == item.itemID)?.itemUnit }}.</td>
                          <td class="px-6 py-2 font-montserrat text-gray-800 w-2/6 text-right">{{
                            parseFloat(item.amountPaid).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div>
                  <div class="px-6 mt-2 mx-2">
                    <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments"
                      v-model="selectedLogDetails.generalNotes" :disabled="true" />
                  </div>
                  <div class="mt-2">
                    <div className="flex flex-row w-full justify-end">
                      <div
                        className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                        <div>Total:</div>
                        <span>{{ parseFloat(selectedLogDetails.profit).toLocaleString('en-PH', {
                          style: 'currency', currency:
                            'PHP'
                        }) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--Preview for Sales-->
              <div v-if="selectedLog && selectedLog.salesID" class="flex flex-col grow min-h-full max-h-full">
                <div class="px-8 pb-2 pt-6 flex justify-between items-center">
                  <div class="w-full flex">
                    <div class="w-3/4 flex flex-col">
                      <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Sales Order #{{
                        selectedLogDetails.id
                      }}</div>
                    </div>
                    <div class="w-1/4 flex flex-col pt-1 items-end">
                      <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">{{
                        selectedLogDetails.salesDate }}
                      </div>
                    </div>
                  </div>
                </div>
                <div class="px-6">
                  <hr class="border-t border-solid border-gray-400 my-2">
                </div>
                <div class="min-h-full max-h-full grow flex flex-col gap-2 justify-between">
                  <div class="grow flex flex-col overflow-x-auto overflow-y-hidden">
                    <div class="min-w-fit grow flex flex-col overflow-x-auto overflow-y-hidden">
                      <table class="table-fixed w-full text-center rounded-md overflow-x-auto">
                        <thead>
                          <tr class="text-savoy-blue leading-4">
                            <th class="px-4 py-2 font-montserrat w-64">Item Name</th>
                            <th class="px-4 py-2 font-montserrat w-32">SRP</th>
                            <th class="px-4 py-2 font-montserrat w-28">Quantity</th>
                            <th class="px-4 py-2 font-montserrat w-32">Amount Due</th>
                            <th class="px-4 py-2 font-montserrat w-32">Subtotal</th>
                            <th class="px-4 py-2 font-montserrat w-32"><span>Discounted Price</span></th>
                            <th class="px-4 py-2 font-montserrat w-32">Net Gain</th>
                          </tr>
                        </thead>
                      </table>
                      <div class="h-32 w-full overflow-y-auto grow">
                        <table class="px-6 table-fixed text-center w-full">
                          <tbody class="min-h-full">
                            <tr v-if="selectedLog !== null" v-for="item in salesItems" class="bg-ghost-white">
                              <td class="px-4 py-2 text-left font-montserrat w-64">
                                <div>{{ props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName }}</div>
                                <div class="pt-2 italic text-sm">{{ props.inventories.find((inventory) => inventory.id ==
                                  item.itemID)?.itemSerialNumber }}</div>
                              </td>
                              <td class="px-4 py-2 font-montserrat text-right w-32">
                                {{ props.inventories.find((inventory) => inventory.id ==
                                  item.itemID)?.itemSRP.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}
                              </td>
                              <td class="px-4 py-2 font-montserrat w-28">
                                {{ item.quantitySold }} {{ props.inventories.find((inventory) => inventory.id ==
                                  item.itemID)?.itemUnit }}.
                              </td>
                              <td class="px-4 py-2 font-montserrat text-right w-32">
                                {{ (item.quantitySold * props.inventories.find((inventory) => inventory.id ==
                                  item.itemID)?.itemSRP).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}
                              </td>
                              <td class="px-4 py-2 font-montserrat text-right w-32">
                                {{ item.amountPaid.toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}
                              </td>
                              <td class="px-4 py-2 font-montserrat text-right w-32">
                                {{ (item.amountPaid / item.quantitySold).toLocaleString('en-PH', {
                                  style: 'currency', currency:
                                    'PHP'
                                }) }}
                              </td>
                              <td class="px-4 py-2 font-montserrat text-right w-32">
                                {{ ((item.amountPaid / item.quantitySold) - props.inventories.find((inventory) => inventory.id
                                  == item.itemID)?.itemCostPrice).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })
                                }}
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <div class="px-6 mb-2 mx-2">
                    <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments"
                      v-model="selectedLogDetails.generalNotes" :disabled="true" />
                  </div>
                  <div class="mt-2">
                    <div className="flex flex-row w-full justify-end">
                      <div
                        className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                        <div>Total:</div>
                        <span>{{ parseFloat(selectedLogDetails.totalSales).toLocaleString('en-PH', {
                          style: 'currency', currency:
                            'PHP'
                        }) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!--Preview for Cashflow-->
              <div v-if="selectedLog && selectedLog.cashflowID" class="flex flex-col grow min-h-full max-h-full">
                <div class="px-8 pb-2 pt-6 flex flex-col justify-between items-center">
                  <div class="w-full flex">
                    <div class="w-3/4 flex flex-col">
                      <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Cashflow #{{
                        selectedLogDetails.id }}
                      </div>
                    </div>
                    <div class="w-1/4 flex flex-col pt-1 items-end">
                      <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">{{
                        new Date(selectedLogDetails.created_at).toISOString().split('T')[0] }}</div>
                    </div>
                  </div>
                </div>
                <div class="px-6">
                  <hr class="border-t border-solid border-gray-400 my-2">
                </div>
                <div class="min-h-full max-h-full grow flex flex-col gap-2 justify-between">
                  <div class="flex flex-col grow">
                    <table class="table-auto w-full rounded-md">
                      <thead>
                        <!-- First row of headers -->
                        <tr class="bg-ghost-white text-savoy-blue">
                          <th class="px-6 py-2 font-montserrat w-3/4">Cashflow Type</th>
                          <th class="px-6 py-2 font-montserrat w-1/4">Amount</th>
                        </tr>
                      </thead>
                      <tbody class="bg-ghost-white max-h-500 overflow-y-auto">
                        <!-- Content row with the same formatting as headers -->
                        <tr class="bg-ghost-white text-savoy-blue">
                          <td class="px-6 py-2 font-montserrat text-gray-800 w-3/4">
                            <div>{{selectedLogDetails.transactionType}}</div>
                          </td>
                          <td class="px-4 py-2 font-montserrat text-gray-800 w-1/4 text-center">{{parseFloat(selectedLogDetails.amount).toLocaleString('en-PH',{style:'currency', currency:'PHP'})}}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="px-6 mt-2 mx-2">
                  <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments"
                    v-model="selectedLogDetails.generalNotes" :disabled="true" />
                </div>
                <div class="mt-2">
                  <div className="flex flex-row w-full justify-end">
                    <div
                      className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                      <div>Total:</div>
                      <span>{{ parseFloat(selectedLogDetails.amount).toLocaleString('en-PH', {
                        style: 'currency', currency:
                          'PHP'
                      }) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <table class="bg-ghost-white w-full rounded-md">
          <thead class="font-bold font-montserrat">
            <tr>
              <div v-if="cashOnHands && cashOnHands.length > 0 && cashOnHands[0].type === 'day_out'">
                <th class="w-3/12 py-2 px-4 text-savoy-blue text-right justify-right">
                  <span class="text-3xl uppercase">
                    {{ discrepancies > 0 ? 'Excess:' : (discrepancies < 0 ? 'Loss:' : 'Discrepancies:') }} </span>
                      <br>
                      <span class="text-sm">(based on cash on hand)</span>
                </th>
                <th class="w-2/12 py-2 px-4" :class="{
                  'text-3xl': true,
                  'text-right': true,
                  'text-persian-red': discrepancies < 0 || discrepancies > 1,
                  'text-dark-pastel-green': discrepancies === 0,
                }">
                  {{
                    parseFloat(discrepancies).toLocaleString('en-PH', {
                      style: 'currency',
                      currency: 'PHP'
                    })
                  }}
                </th>
              </div>
              <th class="w-2/12 py-2 px-4"> </th>
              <th class="w-3/12 py-2 px-4 text-savoy-blue text-right justify-right">
                <span class="text-3xl">NET CASH FLOW:</span> <br>
                <span class="text-sm">(for the day)</span>
              </th>
              <th class="w-2/12 py-2 px-4 text-3xl text-right" :class="{
                'text-persian-red': netCashflow < 0,
                'text-dark-pastel-green': netCashflow >= 0,
              }">{{
                parseFloat(netCashflow).toLocaleString('en-PH', {
                  style: 'currency', currency: 'PHP'
                }) }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </BreezeAuthenticatedLayout>
</template>

<style scoped>
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

.comments {
  height: 85px;
  resize: none;
}

.comments-2 {
  height: 50px;
  resize: none;
}

.icon {
  color: #FFFFFF;
  /* Default icon color */
}

/* Adjust the style for the sticky header */
thead.sticky tr {
  position: sticky;
  top: 0;
  z-index: 1;
}
</style>