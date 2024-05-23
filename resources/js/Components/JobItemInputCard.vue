<script setup>
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, reactive, computed, onMounted, watchEffect } from 'vue';

const {
    jobOrders,
    inventories,
    suppliers,
    selectedItem,
    orderItem,
    orderItemIndex} = defineProps(['jobOrders',
    'inventories',
    'suppliers',
    'selectedItem',
    'orderItem',
    'orderItemIndex'
  ]);

const jobOrderItem = reactive({
  jobOrderID: jobOrders.id,
  itemID: '',
  quantityUsed: '',
  itemName: '',
  itemSerialNumber: '',
  itemSRP: '',
  amountPaid: ''
}); 
const form = useForm({
  quantity: orderItem.quantityUsed || '',
  calculatedPrice: +orderItem.quantityUsed * +selectedItem.itemSRP || '',
  amountPaid: orderItem.amountPaid || ''
});

const confirmItem = ref(false);
const confirm = () => {
  if (form.quantity > 0 && form.quantity <= selectedItem.itemQuantity - selectedItem.itemThreshold && form.amountPaid && form.amountPaid >= 0 && form.amountPaid <= form.calculatedPrice) {
    confirmItem.value = true;
    setJobOrder();
  }
}

onMounted(() => {
  console.log("Order Item inside: " + JSON.stringify(orderItem, null, 2) + " " + orderItemExists.value);
  console.log(selectedItem);
})
const emit = defineEmits();
const setJobOrder = () => {
  console.log("Job Order Being Set");
  console.log(selectedItem);
  jobOrderItem.itemID = selectedItem.itemID;
  jobOrderItem.quantityUsed = form.quantity;
  jobOrderItem.itemName = selectedItem.itemName;
  jobOrderItem.itemSerialNumber = selectedItem.itemSerialNumber;
  jobOrderItem.amountPaid = form.amountPaid;
  jobOrderItem.itemSRP = selectedItem.itemSRP;
  console.log(jobOrderItem);
  if (orderItemExists.value == false) {
    emit("sendJobOrderItem", jobOrderItem, confirmItem.value);
  } else if (orderItemExists.value == true) {
    emit("updateJobOrderItem", jobOrderItem, confirmItem.value, orderItemIndex);
  }
}

const cancel = () => {
  console.log("Order Item inside: " + JSON.stringify(orderItem, null, 2));
  console.log("Order Item inside: " + JSON.stringify(selectedItem, null, 2));
  form.quantity = null;
  form.amountPaid = null;
  emit('close');
}

const calculatePrice = () => {
  const quantity = parseFloat(form.quantity);
  const itemSRP = parseFloat(selectedItem.itemSRP);
  if(!isNaN(quantity) && quantity > 0 && quantity < selectedItem.itemQuantity - selectedItem.itemThreshold){
    form.calculatedPrice = (quantity * itemSRP).toFixed(2);
    console.log('calculatedPrice:', form.calculatedPrice);
    form.amountPaid = form.calculatedPrice;
  }
};

const orderItemExists = ref(false);
watchEffect(() => {
  if (orderItem.quantityUsed !== undefined && orderItem.amountPaid !== undefined &&
    orderItem.quantityUsed !== null && orderItem.amountPaid !== null) {
    console.log("Order Item inside: " + JSON.stringify(orderItem, null, 2));
    orderItemExists.value = true;
  }
});

const editable = ref(false);
const edit = () => {
  editable.value = true;
  document.getElementById('amountPaid').disabled = false;
  document.getElementById('quantity').disabled = false;
}
</script>

<template>
    <div class="flex justify-center items-center h-screen">
      <div class="w-full">
        <div class="mx-auto rounded-lg overflow-hidden bg-ghost-white ">
          <div class="bg-savoy-blue text-white p-4">
            <h2 class="text-2xl font-bold font-montserrat text-center">Job Order: Item</h2>
          </div>
          <form name="createForm" @submit.prevent="submit" class="p-4">
            <div class="flex p-3 px-8 pt-4 pb-6 flex-col">
              <div class="flex flex-row space-x-4 mb-6 p-3 figtree justify-center">
                <div class="flex flex-col items-center justify-center w-1/2 text-center">
                  <BreezeLabel for="itemQuantity" value="Current Quantity" />
                  <div class="flex items-center text-lg">
                    <BreezeTextInput id="itemQuantity" type="input" class="mt-1 px-2 py-1 block"
                      v-model="selectedItem.itemQuantity" :disabled=true />
                  </div>
                </div>
  
                <div class="flex flex-col items-center justify-center w-1/2 text-center">
                  <BreezeLabel for="quantity" value="Quantity" />
                  <div class="relative">
                    <BreezeTextInput id="quantity" type="input" class="mt-1 px-2 py-1 block text-lg" v-model="form.quantity"
                      @input="calculatePrice" :disabled="orderItemExists && !editable" autofocus autocomplete="off" />
                    <span class="error-message text-red-600 text-sm absolute top-full left-0" 
                      v-if="isNaN(form.quantity) || 
                        form.quantity <= 0 || 
                        form.quantity > selectedItem.itemQuantity - selectedItem.itemThreshold" 
                        v-html="isNaN(form.quantity)
                        ? 'Invalid: Enter a numeric value'
                        : form.quantity != '' && form.quantity <= 0
                        ? 'Invalid: Enter a value greater than 0'
                        : form.quantity > selectedItem.itemQuantity - selectedItem.itemThreshold
                        ? 'Invalid: Exceeding item quantity <br> below threshold'
                        : ''">
                    </span>
                  </div>
                </div>
              </div>
  
              <div class="flex flex-row space-x-4 p-3 figtree justify-center">
  
                <div class="flex flex-col items-center justify-center text-center">
                  <BreezeLabel for="itemSRP" value="Suggested Retail Price" />
                  <div class="flex items-center text-lg">
                    <BreezeTextInput id="itemSRP" type="input" class="mt-1 px-2 py-1 block" v-model="selectedItem.itemSRP"
                      :disabled=true />
                  </div>
                </div>
  
                <!--<div class="flex flex-col items-center justify-center text-center">
                  <BreezeLabel for="calculatedPrice" value="Calculated Price" />
                  <div class="flex items-center text-lg">
                    <BreezeTextInput id="calculatedPrice" type="input" class="mt-1 px-2 py-1 block"
                      v-model="form.calculatedPrice" :disabled=true />
                  </div>
                </div>-->
  
                <div class="flex flex-col items-center justify-center text-center">
                  <BreezeLabel for="amountPaid" value="Subtotal" />
                  <div class="relative">
                    <BreezeTextInput id="amountPaid" type="input" class="mt-1 px-2 py-1 block text-lg"
                      v-model="form.amountPaid" :disabled="orderItemExists && !editable" autocomplete="off" />
                    <span class="error-message text-red-600 text-sm absolute top-full left-0"
                      v-if="isNaN(form.amountPaid) || parseFloat(form.amountPaid) > parseFloat(form.calculatedPrice) || parseFloat(form.amountPaid) < 0" 
                      v-html="isNaN(form.amountPaid)
                        ? 'Invalid: Enter a numeric value'
                        : form.amountPaid > form.calculatedPrice
                        ? 'Invalid: Cash amount exceeding <br> calculated price'
                        : form.amountPaid < 0
                        ? 'Invalid: Cash amount must not be <br> less than 0'
                        : ''">
                    </span>
                  </div>
                </div>
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
