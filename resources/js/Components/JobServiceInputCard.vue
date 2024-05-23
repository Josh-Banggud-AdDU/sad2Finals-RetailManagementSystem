<script setup>
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import BreezeTextArea from '@/Components/TextArea.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, reactive, computed, onMounted, watchEffect } from 'vue';

const {
    jobOrders,
    services,
    selectedService,
    orderService,
    orderServiceIndex} = defineProps(['jobOrders',
    'services',
    'selectedService',
    'orderService',
    'orderServiceIndex'
  ]);

const jobOrderService = reactive({
  jobOrderID: jobOrders.id,
  serviceID: '',
  serviceName: '',
  amountPaid: '',
}); 
const form = useForm({
  amountPaid: orderService.amountPaid || ''
});

const confirmService = ref(false);
const confirm = () => {
  if (form.amountPaid && form.amountPaid >= 0) {
    confirmService.value = true;
    setJobOrder();
  }
}

onMounted(() => {
  console.log("Order Service inside: " + JSON.stringify(orderService, null, 2) + " " + orderServiceExists.value);
  console.log(selectedService);
})
const emit = defineEmits();
const setJobOrder = () => {
  console.log("Job Order Being Set");
  console.log(selectedService);
  jobOrderService.serviceID = selectedService.serviceID;
  jobOrderService.serviceName = selectedService.serviceName;
  jobOrderService.amountPaid = form.amountPaid;
  console.log(jobOrderService);
  if (orderServiceExists.value == false) {
    emit("sendJobOrderService", jobOrderService, confirmService.value);
  } else if (orderServiceExists.value == true) {
    emit("updateJobOrderService", jobOrderService, confirmService.value, orderServiceIndex);
  }
}

const cancel = () => {
  console.log("Order Service inside: " + JSON.stringify(orderService, null, 2));
  console.log("Order Service inside: " + JSON.stringify(selectedService, null, 2));
  form.amountPaid = null;
  emit('close');
}

const orderServiceExists = ref(false);
watchEffect(() => {
  if (orderService.amountPaid !== undefined && orderService.amountPaid !== null) {
    console.log("Order Service inside: " + JSON.stringify(orderService, null, 2));
    orderServiceExists.value = true;
  }
});

const editable = ref(false);
const edit = () => {
  editable.value = true;
  document.getElementById('amountPaid').disabled = false;
}
</script>

<template>
    <div class="flex justify-center items-center h-screen">
      <div class="w-full">
        <div class="mx-auto rounded-lg overflow-hidden bg-ghost-white ">
          <div class="bg-savoy-blue text-white p-4">
            <h2 class="text-2xl font-bold font-montserrat text-center">Job Order: Service</h2>
          </div>
          <form name="createForm" @submit.prevent="submit" class="p-4">
            <div class="flex px-8 pb-6 flex-col">
  
              <div class="flex flex-row space-x-4 p-3 mb-3 figtree justify-center">
  
                <div class="flex flex-col items-center justify-center text-center">
                  <BreezeLabel for="amountPaid" value="Subtotal" />
                  <div class="relative">
                    <BreezeTextInput id="amountPaid" type="input" class="mt-1 px-2 py-1 block text-lg"
                      v-model="form.amountPaid" :disabled="orderServiceExists && !editable" autofocus autocomplete="off" />
                      <span class="error-message text-red-600 text-sm absolute top-full left-0" 
                      v-if="isNaN(form.amountPaid) || 
                        form.amountPaid < 0" 
                        v-html="isNaN(form.amountPaid)
                        ? 'Invalid: Enter a numeric value'
                        : form.quantity != '' && form.amountPaid < 0
                        ? 'Invalid: Enter a value greater than <br> or equal to 0'
                        : ''">
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="space-x-6 p-2 flex flex-row justify-center gap-4">
              <button type="button" v-if="orderServiceExists && !editable" @click="edit()"
                class="px-6 py-2 font-bold font-montserrat text-white bg-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue transition-all duration-300 ease-in-out">
                Edit
              </button>
              <button type="button" v-if="!orderServiceExists || (orderServiceExists && editable)" @click="confirm()"
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