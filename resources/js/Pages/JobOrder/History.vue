<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed, onMounted, onUnmounted, reactive } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';

const props = defineProps({
  job_order_history: Array,
  job_order_item_history: Array,
  job_order_service_history: Array,
  job_order_employee_history: Array,
  inventories: Array,
  services: Array,
  employees: Array
});
onMounted(() => {
    console.log(props.job_order_history);
    console.log(props.job_order_item_history);
    console.log(props.job_order_service_history);
    console.log(props.job_order_employee_history);
})
const reversedLogs = computed(() => {
  return [...props.job_order_history].reverse();
});
const selectedLog = ref(null);
const jobOrderItems = ref([]);
const jobOrderServices = ref([]);
const jobOrderEmployees = ref([]);
const handleRowClick = (log) => {
  selectedLog.value = log;
  console.log(selectedLog.value.id);
  jobOrderItems.value = props.job_order_item_history.filter((order) => order.joHistoryID == selectedLog.value.id);
  jobOrderServices.value = props.job_order_service_history.filter((order) => order.joHistoryID == selectedLog.value.id);
  jobOrderEmployees.value = props.job_order_employee_history.filter((order) => order.joHistoryID == selectedLog.value.id);
  console.log(jobOrderItems.value);
  console.log(jobOrderServices.value);
  console.log(jobOrderEmployees.value);
};

const formatServiceID = (service) => {
  return String(service).padStart(5, '0');
};

const closeInputCard = () => {
  console.log("Closing Payment Card");
  isPay.value = false;
}

const receivePayment = (payment) => {
  console.log(payment);
  axios.post(route('job_orders.pay'), {
    jobOrderID: selectedLog.value.id,
    customerPayment: payment
  })
  closeInputCard();
  Inertia.visit(route('job_orders.index'));
}
</script>
<template>
  <Head title="Dashboard" />
  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
        Job Order History
      </h2>
    </template>
    <div class="py-6 w-full flex">
      <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8 grow">
        <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
          <div class="flex items-center justify-between">
            <Link
              class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
              :href="route('job_orders.index')">
            Back
            </Link>
          </div>
        </div>
        <div class="h-32 flex flex-row gap-4 w-full grow">
          <!-- Existing table -->
          <div class="w-2/5 flex flex-col bg-ghost-white rounded-md">
            <table class="table-fixed w-full overflow-hidden rounded-t-md">
              <thead class="sticky top-0 bg-savoy-blue text-white uppercase">
                <tr>
                  <th class="px-2 py-2 w-3/12 font-montserrat text-base">Transaction Date</th>
                  <th class="px-2 py-2 w-3/12 font-montserrat text-base">
                    <span class="inline-block pl-2 -ml-2">Job Order</span>
                  </th>
                  <th class="px-2 py-2 w-4/12 font-montserrat text-base">Total Sales</th>
                </tr>
              </thead>
            </table>
            <div class="h-32 overflow-y-auto rounded-md grow">
              <table class="table-fixed w-full overflow-hidden">
                <tbody class="bg-ghost-white min-h-full">
                  <tr v-for="log in reversedLogs" :key="log.id" @click="handleRowClick(log)" class="hover:bg-silver" >
                    <td class="border-b pl-5 py-2 w-3/12 text-center font-montserrat">{{ log.jobOrderDate }}</td>
                    <td class="border-b pl-10 px-2 py-2 w-3/12 text-left font-montserrat">
                      {{ log.jobOrderStatus }}: <span class="italic text-sm">{{ log.paymentStatus }}</span>
                    </td>
                    <td class="border-b px-6 py-2 w-4/12 text-right font-montserrat">{{parseFloat(log.total).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}</td> 
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
                  Job Order Preview
                </div>
              </div>
              <div v-if="selectedLog !== null" class="flex flex-col grow min-h-full max-h-full">
                <!-- Content to be displayed when a row is clicked -->
                <div class="px-8 pb-2 pt-6 flex justify-between items-center">
                  <div class="w-full flex flex-col">
                    <div class="w-full flex">
                      <div class="w-3/4 flex flex-col">
                        <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Job Order #{{ selectedLog.jobOrderID }}
                        </div>
                        <div class="text-lg font-montserrat font-bold px-1" :class="{
                          'text-saffron': selectedLog.jobOrderStatus == 'Ongoing',
                          'text-persian-red': selectedLog.jobOrderStatus == 'Cancelled',
                          'text-dark-pastel-green': selectedLog.jobOrderStatus == 'Done',
                        }">
                          Status: {{ selectedLog.jobOrderStatus }}
                        </div>
                      </div>
                      <div class="w-1/4 flex flex-col pt-1 items-end">
                        <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">{{
                          selectedLog.jobOrderDate }}</div>
                        <div class="text-lg text-right italic font-montserrat font-bold px-1" :class="{
                          'text-persian-red': selectedLog.paymentStatus == 'Unpaid',
                          'text-dark-pastel-green': selectedLog.paymentStatus == 'Fully Paid',
                        }">
                          {{ selectedLog.paymentStatus }}
                        </div>
                      </div>
                    </div>
                    <div class="w-full font-montserrat px-1">
                      <BreezeTextArea id="vehicleDetails" type="input"
                        className="mt-1 px-2 py-1 rounded-md w-full comments-2 text-sm"
                        placeholder="Vehicle Model, License Plate, Color, etc." v-model="selectedLog.vehicleDetails"
                        :disabled="true" />
                    </div>
                  </div>
                </div>
                <div class="px-6">
                  <hr class="border-t border-solid border-gray-400 my-2">
                </div>
                <div className="min-h-full max-h-full grow flex flex-col gap-2 justify-between">
                  <div class="h-32 overflow-y-auto grow flex flex-col">
                    <table class="table-fixed w-full rounded-md">
                      <thead>
                        <!-- First row of headers -->
                        <tr class="bg-ghost-white text-savoy-blue">
                          <th class="px-6 py-2 font-montserrat w-full">Mechanic Name</th>
                        </tr>
                      </thead>
                      <tbody class="bg-ghost-white max-h-500 overflow-y-auto">
                        <!-- Content row with the same formatting as headers -->
                        <tr v-if="selectedLog !== null" v-for="employee in jobOrderEmployees"
                          class="bg-ghost-white text-savoy-blue">
                          <td class="px-6 py-2 font-montserrat text-gray-800 w-full">
                            <div>{{ props.employees.find((record) => record.id == employee.employeeID)?.firstName }} {{
                              props.employees.find((record) => record.id == employee.employeeID)?.lastName }}</div> <!---->
                            <div class="pt-2 italic text-sm">{{ props.employees.find((record) => record.id ==
                              employee.employeeID)?.number }}</div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <div class="px-6">
                      <hr class="border-t border-solid border-gray-400 my-2">
                    </div>
                    <div>
                      <table class="table-auto w-full rounded-md">
                        <thead>
                          <!-- First row of headers -->
                          <tr class="bg-ghost-white text-savoy-blue">
                            <th class="px-6 py-2 font-montserrat w-4/6">Service Name</th>
                            <th class="px-6 py-2 font-montserrat w-2/6">Subtotal</th>
                          </tr>
                        </thead>
                        <tbody class="bg-ghost-white max-h-500 overflow-y-auto">
                          <!-- Content row with the same formatting as headers -->
                          <tr v-if="selectedLog !== null" v-for="service in jobOrderServices"
                            class="bg-ghost-white text-savoy-blue">
                            <td class="px-6 py-2 font-montserrat text-gray-800 w-3/6">
                              <div>{{ props.services.find((record) => record.id == service.serviceID)?.serviceName }}</div><!---->
                              <div class="pt-2 italic text-sm">{{ formatServiceID(service.serviceID) }}</div><!---->
                            </td>
                            <td class="px-6 py-2 font-montserrat text-gray-800 w-1/5 text-right">{{
                              parseFloat(service.amountPaid).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })
                            }}<!---->
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="px-6">
                      <hr class="border-t border-solid border-gray-400 my-2">
                    </div>
                    <div>
                      <table class="table-auto w-full rounded-md">
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
                              <div>{{ props.inventories.find((inventory) => inventory.id == item.itemID)?.itemName }}</div><!---->
                              <div class="pt-2 italic text-sm">{{ props.inventories.find((inventory) => inventory.id ==
                                item.itemID)?.itemSerialNumber }}</div><!---->
                            </td>
                            <td class="px-4 py-2 font-montserrat text-gray-800 w-1/6 text-center">{{ item.quantityUsed }} {{
                              props.inventories.find((inventory) => inventory.id == item.itemID)?.itemUnit }}.</td><!---->
                            <td class="px-6 py-2 font-montserrat text-gray-800 w-2/6 text-right">{{
                              parseFloat(item.amountPaid).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}<!---->
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="px-6 m-2">
                  <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments"
                    v-model="selectedLog.generalNotes" :disabled="true" />
                </div>
                <div className="flex flex-row justify-end">
                  <div
                    className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                    <div>Total:</div>
                    <span>{{parseFloat(selectedLog.total).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' })}}</span>
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

.comments {
  height: 100px;
  resize: none;
}

.comments-2 {
  height: 50px;
  resize: none;
}

/* Adjust the style for the sticky header */
thead.sticky tr {
  position: sticky;
  top: 0;
  z-index: 1;
}</style>