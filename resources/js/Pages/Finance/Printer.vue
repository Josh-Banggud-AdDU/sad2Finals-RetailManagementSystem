<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue'
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
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
onMounted(() => {
  console.log(props.finances);
})
const reversedFinances = computed(() => {
  return [...props.finances].reverse();
});

const salesItems = ref([]);
const jobOrderItems = ref([]);
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
const selectedYear = ref(currentYear); // Default to the current year

const monthRef = ref(null);
onMounted(() => {
  selectedMonth.value = months[selectedMonth.value - 1].label;
  console.log(selectedMonth.value);

  const handleClickOutsideMonth = (event) => {
        if(monthRef.value && !monthRef.value.contains(event.target)){
            displayMonth.value = false;
        }
    }
  document.addEventListener('mousedown', handleClickOutsideMonth);
  onUnmounted(() => {
      document.removeEventListener('mousedown', handleClickOutsideMonth);
  });
})

const maxAllowedYear = computed(() => currentYear);

const selectedMonthName = computed(() => {
  // You can create an array of month names and use it to display the selected month
  const monthNames = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
  ];
  return monthNames[selectedMonth.value - 1];
});

const displayMonth = ref(false);
const selectMonth = (month) => {
  selectedMonth.value = month.label;
}
const add = () => {
  if(selectedYear.value < maxAllowedYear.value){
    selectedYear.value++;
  }
}
const subtract = () => {
  if(selectedYear.value > 0){
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
    <div class="py-24">
      <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-row gap-4 w-full">
          <!-- Existing table -->
          <div class="flex flex-col space-y-4 w-full">
            <div class="flex flex-col w-full rounded-md">
              <table class="table-auto w-full overflow-hidden divide-x-2 divide-y-2">
                <thead class="sticky border border-black top-0 text-black uppercase">
                  <tr>
                    <th class="px-2 py-2 w-6/12 font-montserrat" colspan="2">Transaction Date & Time</th>
                    <th class="py-2 w-3/12 font-montserrat">Transaction Type</th>
                    <th class="px-2 py-2 w-3/12 font-montserrat">Amount</th>
                  </tr>
                </thead>
              </table>
              <div class="scrollable rounded-md">
                <table class="table-auto w-full overflow-hidden">
                  <tbody class="">
                    <tr v-for="log in reversedFinances" :key="log.id" @click="handleRowClick(log)"
                      class="hover:bg-silver">
                      <template v-if="log.transactionType !== ''">
                        <td class="border border-black pl-6 py-2 w-3/12 text-center font-montserrat">{{ new
                          Date(log.updated_at).toLocaleDateString() }}</td>
                        <td class="border border-black pr-6 py-2 w-3/12 text-center font-montserrat">{{ new
                          Date(log.updated_at).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' }) }}</td>
                        <td class="border border-black px-2 py-2 w-3/12 text-left font-montserrat">{{ log.transactionType }}</td>
                        <td class="border border-black pr-4 py-2 w-3/12 text-right font-montserrat">{{
                          parseFloat(log.amount).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}</td>
                      </template>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <table class="w-full rounded-md">
          <thead class="font-bold font-montserrat">
            <tr>
                <th class="w-3/12 py-2 px-4 text-right justify-right">
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
              <th class="w-2/12 py-2 px-4"> </th>
              <th class="w-3/12 py-2 px-4 text-right justify-right">
                <span class="text-3xl">NET CASH FLOW:</span> <br>
                <span class="text-sm">(for the day)</span>
              </th>
              <th class="w-2/12 py-2 px-4 text-3xl text-right text-dark-pastel-green">{{
                parseFloat(netCashflow).toLocaleString('en-PH', {
                  style: 'currency', currency: 'PHP'
                }) }}</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
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

.scrollable {
  height: 525px;
  overflow-y: auto;
}

.scrollable-2 {
  height: 385px;
  overflow-y: auto;
}

.newscrollable {
  width: 1008px;
  overflow-x: auto;
}

.scrollable-table {
  height: 295px;
  /* Adjusted height */
  overflow-y: auto;
}

.scrollable-table-b {
  height: 295x;
  /* Adjusted height */
  overflow-y: auto;
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