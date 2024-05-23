<script setup>

import { ref, defineEmits, onMounted } from 'vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';

const { suppliers } = defineProps(['suppliers']);
const supplierName = ref("");
const confirmHovered = ref(false);
const cancelHovered = ref(false);
const emit = defineEmits();
const closeModal = () => {
    emit("close");
    confirmHovered.value = false;
    cancelHovered.value = false;
    supplierName.value = "";
}
const confirmAction = () => {
    if(supplierName.value != '' && !suppliers.find((supplier) => supplier.supplierName == supplierName.value)){
        emit("confirmSubmission", supplierName.value);
        closeModal();
    }
}
</script>

<template>
    <div class="flex justify-center items-center h-screen">
        <div class="w-full">
            <div class="mx-auto rounded-lg overflow-hidden bg-ghost-white">
                <div class="bg-savoy-blue text-white p-4">
                    <h2 class="text-2xl font-bold font-montserrat text-center">Add Supplier</h2>
                </div>
                <div class="flex p-3 px-8 pt-4 pb-6 flex-col figtree">
                    <div class="relative text-lg p-4">
                        <BreezeLabel for="supplierName" value="Supplier Name" class="text-lg figtree" />

                        <BreezeTextInput id="supplierName" type="input" class="mt-1 px-2 py-1 block w-96 rounded-md figtree"
                            v-model="supplierName" @input="searchSupplier" @click="setValue" autofocus />
                        <span class="error-message text-red-600 text-sm absolute top-full left-0" 
                            v-html="supplierName.value == ''
                            ? 'Invalid: Enter a value'
                            : suppliers.find((supplier) => supplier.supplierName == supplierName.value)
                                ? 'Invalid: Existing supplier'
                                : ''">
                        </span>
                    </div>
                    <div class="flex justify-center space-x-6 items-center pt-3 gap-4">
                        <button @click="confirmAction"
                            class="btn px-5 py-2 font-bold font-montserrat text-white bg-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue transition-all duration-300 ease-in-out">
                            Confirm
                        </button>
                        <button @click="closeModal"
                            class="btn px-6 py-2 font-bold font-montserrat text-savoy-blue bg-ghost-white border border-savoy-blue border border-savoy-blue rounded-md hover:bg-vista-blue hover:border-vista-blue hover:text-ghost-white transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                    </div>
                </div>
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