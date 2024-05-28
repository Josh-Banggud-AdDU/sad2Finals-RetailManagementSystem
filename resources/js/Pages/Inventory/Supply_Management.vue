<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
  inventories: Array,
  inventory_logs: Array,
  inventory_log_items: Array,
  suppliers: Array,
  employees: Array,
  salesItems: Array
});

const findLogName = (itemID) => {
  const matchingLog = props.inventories.find((inventory) => inventory.id === itemID);
  return matchingLog ? matchingLog.itemName : '';
}
const reversedLogs = computed(() => {
  return [...props.inventory_logs].reverse().filter((log) => {
    const hasLogs = props.inventory_log_items.some((item) => item.inventory_logID == log.id);
    return hasLogs;
  });
});

const selectedLog = ref(null);
const supplyOrderItems = ref([]);
const handleRowClick = (log) => {
  selectedLog.value = log;
  console.log(selectedLog.value);
  supplyOrderItems.value = props.inventory_log_items.filter((item) => item.inventory_logID === selectedLog.value.id);
  calculateReturn();
};

const createStockIn = () => {
  isVisible.value = true;
}

const returnCash = ref(0);
const calculateReturn = () => { 
  returnCash.value = 0;
  supplyOrderItems.value.forEach(item => {
      returnCash.value += item.quantity * (props.salesItems.find((sale) => sale.salesID ==
      selectedLog.value.salesID && sale.itemID == item.itemID)?.amountPaid
      / props.salesItems.find((sale) => sale.salesID == selectedLog.value.salesID && sale.itemID ==
        item.itemID)?.quantitySold);
        console.log(returnCash.value);
  })
  console.log("Calculating Returns: ", returnCash.value);
  return returnCash.value;
}
</script>
<template>
  <Head title="Dashboard" />
  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
        Manage Supply
      </h2>
    </template>
    <div class="py-6 w-full flex">
      <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8 grow">
        <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
          <div class="flex items-center justify-between">
            <Link
              class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
              :href="route('inventories.main')">
            Back
            </Link>
          </div>
        </div>
        <div class="grid grid-cols-3 gap-4 w-full">
          <div class="flex items-center">
            <Link
              class="w-full flex justify-center px-6 py-2 text-white font-montserrat bg-saffron text-lg font-bold rounded-md focus:outline-none hover:bg-ecru transition-all duration-300 ease-in-out"
              :href="route('inventory_logs.item_return')">
            Item Return
            </Link>
          </div>
          <div class="flex items-center">
            <Link
              class="w-full flex justify-center px-6 py-2 text-white font-montserrat bg-dark-pastel-green text-lg font-bold rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
              :href="route('inventory_logs.stock_in')">
            Stock In
            </Link>
          </div>
          <div class="flex items-center">
            <Link
              class="w-full flex justify-center px-6 py-2 text-white font-montserrat bg-persian-red text-lg font-bold rounded-md focus:outline-none hover:bg-light-coral transition-all duration-300 ease-in-out"
              :href="route('inventory_logs.stock_out')">
            Stock Out
            </Link>
          </div>
        </div>
        <div class="h-32 flex flex-row gap-4 w-full grow">
          <!-- Existing table -->
          <div class="w-2/5 flex flex-col bg-ghost-white rounded-md">
            <table class="table-fixed w-full overflow-hidden rounded-t-md">
              <thead class="sticky top-0 bg-savoy-blue text-white uppercase">
                <tr>
                  <th class="px-2 py-2 w-1/4 font-montserrat">Transaction Date</th>
                  <th class="px-2 py-2 w-1/4 font-montserrat">
                    <span class="inline-block pl-2 -ml-2">Transaction Type</span>
                  </th>
                </tr>
              </thead>
            </table>
            <div class="h-32 overflow-y-auto rounded-md grow">
              <table class="table-fixed w-full overflow-hidden ">
                <tbody class="bg-ghost-white min-h-full">
                  <tr v-for="log in reversedLogs" :key="log.id" @click="handleRowClick(log)" class="hover:bg-silver">
                    <template v-if="log.transactionType !== ''">
                      <td class="border-b px-2 py-2 max-w-xs text-center font-montserrat">{{ log.transactionDate }}</td>
                      <td class="border-b px-2 py-2 max-w-xs text-center font-montserrat">{{ log.transactionType }}</td>
                    </template>
                    <!-- kept? old code from inventory log cc: @josh
                      <template v-if="log.transactionType !== ''">
                      </template>
                    -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- New table -->
          <div class="h-full w-3/5 grow">
            <div class="flex flex-col bg-ghost-white rounded-md p-4 min-h-full max-h-full">
              <div v-if="selectedLog === null" class="flex items-center justify-center grow">
                <!-- Placeholder content when no row is clicked -->
                <div class="text-5xl font-montserrat text-savoy-blue" style="color: #BFBFBF;">
                  Supply Log Preview
                </div>
              </div>
              <div v-if="selectedLog !== null" class="flex flex-col grow min-h-full max-h-full">
                <!-- Content to be displayed when a row is clicked -->
                <div class="px-8 pb-2 pt-6 flex justify-between items-center">
                  <div class="w-full flex">
                    <div class="w-3/4 flex flex-col">
                      <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">
                        Supply Log #{{ selectedLog.id }}
                      </div>
                      <div class="text-lg font-montserrat text-savoy-blue font-bold px-1" v-html="selectedLog.transactionType != 'Item Return'
                        ? 'Reference Number: ' + selectedLog.referenceNum
                        : 'Return Under Sales Order #' + selectedLog.salesID">

                      </div>
                      <div class="text-sm font-montserrat font-bold px-1" :class="{
                        'text-dark-pastel-green': selectedLog.transactionType === 'Stock In',
                        'text-persian-red': selectedLog.transactionType === 'Stock Out',
                        'text-saffron': selectedLog.transactionType === 'Item Return'
                      }">
                        {{ selectedLog.transactionType }} {{ props.suppliers.find((supplier) => supplier.id ==
                          selectedLog.supplierID) ? ": " + props.suppliers.find((supplier) => supplier.id ==
                            selectedLog.supplierID).supplierName : "" }}</div>
                    </div>
                    <div class="w-1/4 flex flex-col pt-1 items-end">
                      <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">{{
                        selectedLog.transactionDate }}</div>
                      <div class="text-sm font-bold font-montserrat text-right leading-5 text-savoy-blue p-1">{{
                        props.employees.find((employee) => employee.id == selectedLog.employeeID)?.firstName }}
                        {{ props.employees.find((employee) => employee.id == selectedLog.employeeID)?.lastName }}</div>
                    </div>
                  </div>
                </div>
                <div class="px-6">
                  <hr class="border-t border-solid border-gray-400 my-2">
                </div>
                <div class="min-h-full max-h-full grow flex flex-col gap-2 justify-between">
                  <div class="grow flex flex-col">
                    <!--Stock In & Stock Out Details-->
                    <div v-if="selectedLog.transactionType != 'Item Return'" class="flex flex-col grow">
                      <table class="px-6 table-fixed w-full rounded-md">
                        <thead>
                          <!-- First row of headers -->
                          <tr class="bg-ghost-white text-savoy-blue">
                            <th class="px-6 py-2 font-montserrat w-4/5">Item Name</th>
                            <th class="px-6 py-2 font-montserrat w-1/5">Quantity</th>
                          </tr>
                        </thead>
                      </table>
                      <div class="h-32 overflow-y-auto grow">
                        <table class="px-6 table-fixed w-full overflow-hidden">
                          <tbody class="bg-ghost-white min-h-full">
                            <!-- Content row with the same formatting as headers -->
                            <tr v-if="selectedLog !== null" v-for="item in supplyOrderItems"
                              class="bg-ghost-white text-savoy-blue">
                              <td class="px-6 py-2 font-montserrat text-gray-800 w-4/5">
                                <div>{{ props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName }}</div>
                                <div class="pt-2 italic text-sm">{{ props.inventories.find((inventory) => inventory.id ==
                                  item.itemID)?.itemSerialNumber }}</div>
                              </td>
                              <td class="px-6 py-2 font-montserrat text-gray-800 w-1/5 text-center">{{ item.quantity }} {{
                                props.inventories.find((inventory) => inventory.id == item.itemID)?.itemUnit }}.</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <!--Item Return Details-->
                    <div v-if="selectedLog.transactionType == 'Item Return'" class="flex flex-col grow">
                      <table class="px-6 table-fixed w-full rounded-md">
                        <thead>
                          <!-- First row of headers -->
                          <tr class="bg-ghost-white text-savoy-blue">
                            <th class="px-6 py-2 font-montserrat w-3/6">Item Name</th>
                            <th class="px-6 py-2 font-montserrat w-1/6">Quantity</th>
                            <th class="px-6 py-2 font-montserrat w-2/6">Amount Returned</th>
                          </tr>
                        </thead>
                      </table>
                      <div class="h-32 overflow-y-auto grow">
                        <table class="px-6 table-fixed w-full overflow-hidden">
                          <tbody class="bg-ghost-white min-h-full">
                            <!-- Content row with the same formatting as headers -->
                            <tr v-if="selectedLog !== null" v-for="item in supplyOrderItems"
                              class="text-savoy-blue">
                              <td class="px-6 py-2 font-montserrat text-gray-800 w-3/6">
                                <div>{{ props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName }}</div>
                                <div class="pt-2 italic text-sm">{{ props.inventories.find((inventory) => inventory.id ==
                                  item.itemID)?.itemSerialNumber }}</div>
                              </td>
                              <td class="px-6 py-2 font-montserrat text-gray-800 w-1/6 text-center">{{ item.quantity }} {{
                                props.inventories.find((inventory) => inventory.id == item.itemID)?.itemUnit }}.</td>
                              <td class="px-6 py-2 font-montserrat text-gray-800 w-2/6 text-right">{{
                                parseFloat(item.quantity * (props.salesItems.find((sale) => sale.salesID ==
                                  selectedLog.salesID && sale.itemID == item.itemID)?.amountPaid
                                  / props.salesItems.find((sale) => sale.salesID == selectedLog.salesID && sale.itemID ==
                                    item.itemID)?.quantitySold)).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })
                              }}</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                  <div>
                    <div class="px-6 m-2">
                      <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments"
                        v-model="selectedLog.generalNotes" :disabled="true" />
                    </div>
                    <div v-if="selectedLog.transactionType == 'Item Return'" className="flex flex-row justify-between">
                      <button type="submit" @click="submit"
                        className="px-6 py-2 text-white font-montserrat bg-dark-pastel-green font-bold rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                        Save
                      </button>
                      <div
                        className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                        <div>Total:</div>
                        <span>{{ parseFloat(returnCash ? returnCash : 0).toLocaleString('en-PH', {
                          style: 'currency', currency:
                            'PHP'
                        }) }}
                        </span>
                      </div>
                    </div>
                  </div>
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
  height: 395px;
  overflow-y: auto;
}

.scrollable-2 {
  height: 350px;
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
}</style>