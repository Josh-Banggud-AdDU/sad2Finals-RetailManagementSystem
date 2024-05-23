<script setup>
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, reactive, computed, onMounted, watchEffect } from 'vue';

const {
  inventory_logs,
  inventories,
  suppliers,
  selectedItem,
  orderItem,
  orderItemIndex,
  transaction,
  salesItems,
  itemReturns,
  inventory_logs_items} = defineProps(['inventory_logs',
    'inventories',
    'suppliers',
    'selectedItem',
    'orderItem',
    'orderItemIndex',
    'transaction',
    'salesItems',
    'itemReturns',
    'inventory_logs_items'
  ]);

const supplyOrderItem = reactive({
  inventory_logID: inventory_logs.id,
  itemID: '',
  quantity: '',
  generalNotes: '',
  itemName: '',
  itemSerialNumber: ''
});
const form = useForm({
  quantity: orderItem.quantity || '',
  resultingQuantity: (transaction === 'Stock In' || transaction === 'Item Return') && orderItem.quantity ? +selectedItem.itemQuantity + +orderItem.quantity : transaction === 'Stock Out' && orderItem.quantity ? +selectedItem.itemQuantity - +orderItem.quantity : '',
  generalNotes: orderItem.generalNotes || ''
});

const confirmItem = ref(false);
const confirm = () => {
  if(transaction == 'Item Return'){
    if(form.quantity > 0 && form.quantity <= availableQuantity.value){
      confirmItem.value = true;
      setSupplyOrder();
    }
  }else{
    if (form.quantity > 0) {
    confirmItem.value = true;
    setSupplyOrder();
  }
  }
}

const soldItem = ref({});
const returnList = ref([]);
const itemReturn = ref([]);
const returnItemQuantity = ref(0);
const availableQuantity = ref(0);
onMounted(() => {
  if(transaction == 'Item Return'){
    soldItem.value = salesItems.find((item) => item.itemID === selectedItem.itemID);
    //Generates an array of inventory_log IDs of logs that are both item returns referencing the same salesID
    returnList.value = itemReturns ? itemReturns.map((log) => log.id) : [];
    //Generates an array of items filtered from inventory_logs_items based on the IDs from returnList
    itemReturn.value = inventory_logs_items.filter((item) => returnList.value.includes(item.inventory_logID) &&  item.itemID == selectedItem.itemID);
    returnItemQuantity.value = itemReturn.value ? itemReturn.value.reduce((sum, item) => sum + item.quantity, 0) : 0;
    availableQuantity.value = parseFloat(soldItem.value.quantitySold) - parseFloat(returnItemQuantity.value);
    form.quantity = orderItemExists.value ? orderItem.quantity : availableQuantity.value;

    console.log("Quantity of Returned Item: ", returnItemQuantity.value);
    console.log("Item Returns: ", itemReturns);
    console.log("List of Item Return IDs: ", returnList.value);
    console.log("List of Sales Items: ", salesItems);
    console.log("Item Sold: ", soldItem.value);
    console.log("Returned Item: ", itemReturn.value);
    console.log("Available Quantity for Return", form.quantity);
  }
  console.log("Order Item inside: " + JSON.stringify(orderItem, null, 2) + " " + orderItemExists.value);
  console.log("Selected Item: ", selectedItem);
})
const emit = defineEmits();
const setSupplyOrder = () => {
  console.log("Supply Order Being Set");
  console.log(selectedItem);
  const supplyItem = ref(inventories.find((item) => item.id == selectedItem.itemID));
  const supplyItemName = ref(supplyItem.value.itemName);
  const supplyItemSerialNumber = ref(supplyItem.value.itemSerialNumber);
  supplyOrderItem.itemID = selectedItem.itemID;
  supplyOrderItem.quantity = form.quantity;
  supplyOrderItem.generalNotes = form.generalNotes;
  supplyOrderItem.itemName = supplyItemName.value;
  supplyOrderItem.itemSerialNumber = supplyItemSerialNumber.value;
  console.log(supplyOrderItem);
  if (orderItemExists.value == false) {
    emit("sendSupplyOrderItem", supplyOrderItem, confirmItem.value);
  } else if (orderItemExists.value == true) {
    emit("updateSupplyOrderItem", supplyOrderItem, confirmItem.value, orderItemIndex);
  }
}

const cancel = () => {
  console.log("Order Item inside: " + JSON.stringify(orderItem, null, 2));
  console.log("Order Item inside: " + JSON.stringify(selectedItem, null, 2));
  form.quantity = null;
  form.generalNotes = null;
  emit('close');
}

const calculateResultingQuantity = () => {
  const currentQuantity = parseFloat(selectedItem.itemQuantity) || 0;
  const quantityChange = parseFloat(form.quantity) || 0;
  const availableQuantity = parseFloat(soldItem.value.quantitySold) - parseFloat(returnItemQuantity.value);
  
  if (quantityChange <= 0) {
    form.errors.quantity = 'Invalid: Enter a value greater than 0';
  }else if(transaction == "Item Return" && quantityChange > availableQuantity){
    form.errors.quantity = 'Invalid: Exceeding available quantity for return';
  } else {
    const transactionType = ref(transaction);
    form.errors.quantity = '';
    if (transactionType.value == "Stock In" || transactionType.value == "Item Return") {
      console.log('currentQuantity:', currentQuantity);
      console.log('quantityChange:', quantityChange);

      form.resultingQuantity = (currentQuantity + quantityChange).toFixed(2);

      console.log('resultingQuantity:', form.resultingQuantity);
    } else if (transactionType.value == "Stock Out") {
      console.log('currentQuantity:', currentQuantity);
      console.log('quantityChange:', quantityChange);

      form.resultingQuantity = (currentQuantity - quantityChange).toFixed(2);

      console.log('resultingQuantity:', form.resultingQuantity);
    }
  }
};

const orderItemExists = ref(false);
watchEffect(() => {
  if (orderItem.quantity !== undefined && orderItem.generalNotes !== undefined &&
    orderItem.quantity !== null && orderItem.generalNotes !== null) {
    console.log("Order Item inside: " + JSON.stringify(orderItem, null, 2));
    orderItemExists.value = true;
  }
});

const editable = ref(false);
const edit = () => {
  editable.value = true;
  document.getElementById('generalNotes').disabled = false;
  document.getElementById('quantity').disabled = false;
}

</script>

<template>
  <div class="flex justify-center items-center h-screen">
    <div class="w-full">
      <div class="mx-auto rounded-lg overflow-hidden bg-ghost-white">
        <div class="bg-savoy-blue text-white p-4">
          <h2 class="text-2xl font-bold font-montserrat text-center">Stock Management Order</h2>
        </div>
        <form name="createForm" @submit.prevent="submit" class="p-4">
          <div class="flex p-3 px-8 pt-4 pb-6 flex-col">
            <div v-if="salesItems" class="flex flex-row space-x-4 mb-2 figtree justify-center">
              <div class="flex flex-col items-center w-1/2 justify-center text-center">
                <BreezeLabel for="salesItemQuantity" value="Sold Quantity" />
                <div class="flex items-center text-lg">
                  <BreezeTextInput id="soldQuantity" type="input" class="mt-1 px-2 py-1 block"
                    v-model="soldItem.quantitySold" :disabled=true />
                </div>
              </div>
              <div class="flex flex-col items-center w-1/2 justify-center text-center">
                <BreezeLabel for="returnedQuantity" value="Returned Quantity" />
                <div class="flex items-center text-lg">
                  <BreezeTextInput id="returnedQuantity" type="input" class="mt-1 px-2 py-1 block"
                    v-model="returnItemQuantity" :disabled=true />
                </div>
              </div>
            </div>
            <div class="flex flex-row space-x-4 mb-2 figtree justify-center">
              <div class="flex flex-col items-center justify-center text-center">
                <BreezeLabel for="itemQuantity" value="Current Quantity" />
                <div class="flex items-center text-lg">
                  <BreezeTextInput id="itemQuantity" type="input" class="mt-1 px-2 py-1 block"
                    v-model="selectedItem.itemQuantity" :disabled=true />
                </div>
              </div>
              <div></div>
              <div class="flex flex-col items-center">
                <BreezeLabel for="quantity" value="Quantity Change" />
                <div class="relative">
                  <BreezeTextInput id="quantity" type="input" class="mt-1 px-2 py-1 block text-lg" v-model="form.quantity"
                    @input="calculateResultingQuantity" :disabled="orderItemExists && !editable" autocomplete="off"/>
                  <span class="error-message text-red-600 text-sm absolute top-full left-0" v-if="form.errors.quantity">
                    {{ form.errors.quantity }}
                  </span>
                </div>
              </div>
              <div class="flex flex-col items-center justify-center text-center">
                <BreezeLabel for="resultingQuantity" value="Resulting Quantity" />
                <div class="flex items-center text-lg">
                  <BreezeTextInput id="resultingQuantity" type="input" class="mt-1 px-2 py-1 block"
                    v-model="form.resultingQuantity" :disabled=true />
                </div>
              </div>
            </div>
            <div class="mb-2">
              <BreezeLabel for="generalNotes" value="General Notes" />
              <BreezeTextArea id="generalNotes" type="input" class="mt-1 px-2 py-1 block w-full text-lg"
                v-model="form.generalNotes" :disabled="orderItemExists && !editable" />
            </div>
          </div>
          <div class="space-x-6 p-2 flex flex-row gap-4">
            <button type="button" v-if="orderItemExists && !editable" @click="edit()"
              class="px-6 py-2 font-bold font-montserrat text-white bg-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue transition-all duration-300 ease-in-out">
              Edit
            </button>
            <button type="button" v-if="!orderItemExists || (orderItemExists && editable)" @click="confirm()"
              class="px-5 py-2 font-bold font-montserrat text-white bg-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue transition-all duration-300 ease-in-out">
              Confirm
            </button>
            <button type="button" @click="cancel()"
              class="px-6 py-2 font-bold font-montserrat text-savoy-blue bg-ghost-white border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue hover:text-ghost-white transition-all duration-300 ease-in-out">
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
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
</style>
