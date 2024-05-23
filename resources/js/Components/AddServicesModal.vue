<script setup>

import { ref, defineEmits, onMounted } from 'vue';
import { useForm } from '@inertiajs/inertia-vue3';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';

const {services} = defineProps(['services']);

const form = useForm({
    serviceName: '',
});
const confirmHovered = ref(false);
const cancelHovered = ref(false);
const searchService = () => {
    if(form.serviceName == ''){
        form.errors.serviceName = 'Invalid: Enter a value';
    }else if(!!services.find((service) => service.serviceName == form.serviceName)){
        form.errors.serviceName = 'Invalid: Existing service';
    }else{
        form.errors.serviceName = '';
    }
}
const emit = defineEmits();
const closeModal = () => {
    emit("close");
    confirmHovered.value = false;
    cancelHovered.value = false;
    form.serviceName = "";
}
const confirmAction = () => {
    searchService();
    if(form.errors.serviceName == ''){
        emit("confirmSubmission", form.serviceName);
        closeModal();
    }
}
</script>

<template>
    <div class="flex justify-center items-center h-screen">
        <div class="w-full">
            <div class="mx-auto rounded-lg overflow-hidden bg-ghost-white">
                <div class="bg-savoy-blue text-white p-4">
                    <h2 class="text-2xl font-bold font-montserrat text-center">Add Service</h2>
                </div>
                <div class="flex p-3 px-8 pt-4 pb-6 flex-col figtree">
                    <div class="relative text-lg px-4 pt-4 mb-8">
                        <BreezeLabel for="serviceName" value="Service Name" class="text-lg figtree" />

                        <BreezeTextInput id="serviceName" type="input"
                            class="mt-1 px-2 py-1 block w-96 rounded-md figtree" v-model="form.serviceName"
                            @input="searchService" autofocus autocomplete="off"/>
                        <span className="error-message text-red-600 text-sm text-center absolute top-full left-0" v-if="form.errors.serviceName">
                            {{ form.errors.serviceName }}
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