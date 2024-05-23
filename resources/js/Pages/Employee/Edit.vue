<script setup>
import { ref } from 'vue';
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { onMounted, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import  axios  from 'axios';

const props = defineProps({
    employees: Object
});

const editable = ref(false);
const cancelEdit = () => {
    editable.value = false;
    form.lastName = props.employees.lastName;
    form.firstName = props.employees.firstName;
    form.number = props.employees.number;
    form.address = props.employees.address;
    form.position = props.employees.position;
};

const form = useForm({
    lastName: props.employees.lastName,
    firstName: props.employees.firstName,
    number: props.employees.number,
    address: props.employees.address,
    position: props.employees.position,
});

const submit = async () => {
    if(
        form.firstName == '' ||
        form.lastName == '' ||
        !phoneNumberPattern.test(form.number) ||
        form.position == '' ||
        form.address == ''
    ){
        isVisible.value = true;
        displayInvalid();
    }else{
        isVisible2.value = true;
    }
};

const phoneNumberPattern = /^09\d{9}$/;
const displayInvalid = (event) => {
    const id = event ? event.target.id : null;
    if(id == "firstName"){
        if(form.firstName == ''){
            form.errors.firstName = 'Invalid: Enter a value'
        }else{
            form.errors.firstName = ''
        }
    }else if(id == "lastName"){ 
        if(form.lastName == ''){
            form.errors.lastName = 'Invalid: Enter a value'
        }else{
            form.errors.lastName = ''
        }
    }else if(id == "number"){
        if(!phoneNumberPattern.test(form.number)){
            form.errors.number = 'Invalid: Phone number not allowed'
        }else{
            form.errors.number = ''
        }
    }else if(id == "position"){
        if(form.position == ''){
            form.errors.position = 'Invalid: Enter a value'
        }else{
            form.errors.position = ''
        }
    }else if(id == "address"){
        if(form.address == ''){
            form.errors.address = 'Invalid: Enter a value'
        }else{
            form.errors.address = ''
        }
    }else if(id == null){
        if(form.firstName == ''){
            form.errors.firstName = 'Invalid: Enter a value'
        }else{
            form.errors.firstName = ''
        }
        if(form.lastName == ''){
            form.errors.lastName = 'Invalid: Enter a value'
        }else{
            form.errors.lastName = ''
        }
        if(!phoneNumberPattern.test(form.number)){
            form.errors.number = 'Invalid: Phone number not allowed'
        }else{
            form.errors.number = ''
        }
        if(form.position == ''){
            form.errors.position = 'Invalid: Enter a value'
        }else{
            form.errors.position = ''
        }
        if(form.address == ''){
            form.errors.address = 'Invalid: Enter a value'
        }else{
            form.errors.address = ''
        }
    }
}

const isVisible = ref(false);
const isVisible2 = ref(false);
const isVisible3 = ref(false);
const closeWarning = () => {
    isVisible.value = false;
    isVisible2.value = false;
    isVisible3.value = false;
}
const confirm = () => {
    isVisible2.value = false;
    form.put(route('employees.update', props.employees.id));
}

const exit = () => {
    isVisible3.value = false;
    Inertia.visit(route('employees.index'));
}
</script> 
<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                {{ editable ? 'Edit Item' : 'View Item' }}
            </h2>
        </template>
        <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningIncompleteModal @close="closeWarning" />
        </div>
        <div v-if="isVisible2" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmInputModal @confirmSubmission="confirm" @close="closeWarning" />
        </div>
        <div v-if="isVisible3" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmBackModal @confirmSubmission="exit" @close="closeWarning" />
        </div>
        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <button @click="isVisible3 = true"
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            >
                        Back
                        </button>
                        <!--<Link
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            :href="route('employees.index')">
                        Back
                        </Link>-->
                    </div>
                </div>
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-ghost-white noto-sans border-b border-gray-200">
                        <form name="createForm" @submit.prevent="submit">
                            <div className="flex flex-col">
                                <div className="flex flex-row w-full figtree mb-4 space-x-4">
                                    <div className="w-11/12">
                                        <BreezeLabel for="firstName" value="First Name" />

                                        <BreezeTextInput id="firstName" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.firstName" :disabled="!editable" @blur="displayInvalid" autofocus autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.firstName">
                                            {{ form.errors.firstName }}
                                        </span>
                                    </div>
                                    <div className="w-11/12">
                                        <BreezeLabel for="lastName" value="Last Name" />

                                        <BreezeTextInput id="lastName" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.lastName" :disabled="!editable"  @blur="displayInvalid" autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.lastName">
                                            {{ form.errors.lastName }}
                                        </span>
                                    </div>
                                </div>
                                <div className="flex flex-row w-full mb-4 space-x-4">
                                    <div className="w-1/6">
                                        <BreezeLabel for="number" value="Phone Number" />

                                        <BreezeTextInput id="number" type="input"
                                            class="mt-1 px-2 py-1 block w-full" v-model="form.number" 
                                            :disabled="!editable"  @blur="displayInvalid" autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.number">
                                            {{ form.errors.number }}
                                        </span>
                                    </div>
                                    <div className="w-1/6">
                                        <BreezeLabel for="position" value="Position" />
                                        <select id="position" v-model="form.position" @blur="displayInvalid" :disabled="!editable"
                                            class="mt-1 px-2 py-1 block w-full shadow-sm border-none rounded-md">
                                            <option value="Mechanic">Mechanic</option>
                                            <option value="Clerk">Clerk</option>
                                            <option value="Admin">Admin</option>
                                        </select>
                                        <span className="text-red-600" v-if="form.errors.position">
                                            {{ form.errors.position }}
                                        </span>
                                    </div>
                                    <div className="grow">
                                        <BreezeLabel for="address" value="Home Address" />

                                        <BreezeTextInput id="address" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.address" :disabled="!editable"  @blur="displayInvalid" autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.address">
                                            {{ form.errors.address }}
                                        </span>
                                    </div>
                                    <!--
                                    <div className="w-2/4">
                                        <BreezeLabel for="email" value="Email" />

                                        <BreezeTextInput id="email" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.email" autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.email">
                                            {{ form.errors.email }}
                                        </span>
                                    </div>
                                    <div className="w-1/4">
                                        <BreezeLabel for="password" value="Password" />

                                        <BreezeTextInput id="password" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.password" autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.password">
                                            {{ form.errors.password }}
                                        </span>
                                    </div>-->
                                </div>
                                <div className="flex flex-row w-full space-x-4 mb-4">
                                </div>
                            </div>
                            <div className="flex flex-row gap-4 mt-4">
                                <button type="button" v-if="!editable" @click="editable = true"
                                    className="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue">
                                    Edit
                                </button>
                                <button type="submit" v-if="editable"
                                    className="px-6 py-2 text-white font-montserrat bg-dark-pastel-green font-bold rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                    Save
                                </button>
                                <button type="button" v-if="editable"
                                    className="px-6 py-2 text-white font-montserrat bg-persian-red font-bold rounded-md hover:bg-light-coral transition-all duration-300 ease-in-out"
                                    @click="cancelEdit">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>