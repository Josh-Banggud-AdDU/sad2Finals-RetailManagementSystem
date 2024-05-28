<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  sales: Array,
  salesItems: Array,
  inventories: Array
});

const filterSalesItems = (salesID) => {
  const filteredItems = props.salesItems.filter((item) => item.salesID === salesID);
  return filteredItems.slice(0, 3);
}
const reversedSales = computed(() => {
  return [...props.sales].reverse().filter((sales) => {
    const hasItems = props.salesItems.some((item) => item.salesID === sales.id)
    return hasItems;
  });
});
const selectedLog = ref(null);
const salesOrderItems = ref([])
const handleRowClick = (log) => {
  selectedLog.value = log;
  console.log(selectedLog.value);
  salesOrderItems.value = props.salesItems.filter((item) => item.salesID === selectedLog.value.id);
};
const concatenateSalesItems = (items) => {
  if (!items || items.length == 0) {
    return '';
  }
  const concatenatedItems = items.map((item) => {
    const matchingInventory = props.inventories.find((inventory) => inventory.id === item.itemID);
    return matchingInventory ? matchingInventory.itemName : ''; // Use itemName from the matching inventory
  }).join(', ');

  return concatenatedItems;
};

</script>
<template>
  <Head title="Dashboard" />
  <BreezeAuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
        Sales History
      </h2>
    </template>
    <div class="py-6 w-full flex">
      <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8 grow">
        <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
          <div class="flex items-center justify-between">
            <Link
              class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
              :href="route('sales.main')">
            Back
            </Link>
          </div>
        </div>
        <!--grow test-->
        <div class="flex flex-row gap-4 w-full grow">
          <!-- Existing table -->
          <div class="w-2/5 flex flex-col bg-ghost-white rounded-md">
            <table class="table-fixed w-full overflow-hidden rounded-t-md">
              <thead class="sticky top-0 bg-savoy-blue text-white uppercase">
                <tr>
                  <th class="px-2 py-2 w-4/12 font-montserrat">Transaction Date</th>
                  <th class="px-2 py-2 w-4/12 font-montserrat">
                    <span class="inline-block pl-2">Sales Order</span>
                  </th>
                  <th class="px-2 py-2 w-4/12 font-montserrat">Total Sales</th>
                </tr>
              </thead>
            </table>
            <div class="h-32 overflow-y-auto rounded-md grow">
              <table class="table-fixed w-full overflow-hidden">
                <tbody class="bg-ghost-white min-h-full">
                  <tr v-for="sales in reversedSales" :key="sales.id" @click="handleRowClick(sales)"
                    class="hover:bg-silver">
                      <td class="border-b px-2 py-2 w-4/12 text-center font-montserrat">{{ sales.salesDate }}</td>
                      <td class="border-b w-4/12 text-center">
                        <div class="px-3 py-2 font-montserrat">
                          Sales Order #{{ sales.id}}
                        </div>
                      </td>
                      <td class="border-b px-6 py-2 w-4/12 text-right font-montserrat">{{ parseFloat(sales.totalSales).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- New table -->
          <div class="h-full w-2/5 grow">
            <div class="flex flex-col bg-ghost-white rounded-md p-4 min-h-full max-h-full">
              <div v-if="selectedLog === null" class="flex items-center justify-center grow">
                <!-- Placeholder content when no row is clicked -->
                <div class="text-5xl font-montserrat text-savoy-blue" style="color: #BFBFBF;">
                  Sales Preview
                </div>
              </div>
              <div v-if="selectedLog !== null" class="flex flex-col grow min-h-full max-h-full">
                <!-- Content to be displayed when a row is clicked -->
                <div class="px-8 pb-2 pt-6 flex justify-between items-center">
                  <div class="w-full flex">
                    <div class="w-3/4 flex flex-col">
                      <div class="text-3xl font-montserrat font-bold text-savoy-blue px-1">Sales Order #{{ selectedLog.id
                      }}</div>
                    </div>
                    <div class="w-1/4 flex flex-col pt-1 items-end">
                      <div class="text-2xl font-montserrat font-bold text-right text-savoy-blue px-1">{{ selectedLog.salesDate }}
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
                      <table class="table-fixed w-full rounded-md text-center">
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
                        <table class="px-6 table-fixed w-full text-center">
                          <tbody class="min-h-full">
                            <tr v-if="selectedLog !== null" v-for="item in salesOrderItems">
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
                                {{ item.quantitySold }} {{ props.inventories.find((inventory) => inventory.id == item.itemID)?.itemUnit }}.
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
                                  == item.itemID)?.itemCostPrice).toLocaleString('en-PH', { style: 'currency', currency: 'PHP' }) }}
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <div class="px-6 m-2">
                    <BreezeTextArea id="generalNotes" type="input" className="mt-1 px-2 py-1 rounded-md w-full comments"
                      v-model="selectedLog.generalNotes" :disabled="true" />
                  </div>
                  <div>
                    <div className="flex flex-row justify-end">
                      <div
                        className="flex flex-row justify-between w-1/2 text-3xl font-montserrat font-bold text-savoy-blue px-1">
                        <div>Total:</div>
                        <span>{{ parseFloat(selectedLog.totalSales).toLocaleString('en-PH', {
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
  height: 510px;
  /* Adjusted height */
  overflow-y: auto;
}

.newscrollable {
  width: 1008px;
  overflow-x: auto;
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

.line-clamp-4 {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

</style>