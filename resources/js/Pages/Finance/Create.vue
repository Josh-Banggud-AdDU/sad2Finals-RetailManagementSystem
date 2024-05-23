<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue'
import { Head, Link, useForm, defineProps } from '@inertiajs/inertia-vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    cashflows: Array
})

const form = useForm({
    amount: '',
    referenceNumber: '',
    generalNotes: '',
    transactionType: 'Expense',
});


const submit = async () => {
    form.post(route('cashflows.store'), {
        amount: form.amount,
        referenceNumber: form.referenceNumber,
        generalNotes: form.generalNotes,
        transactionType: form.transactionType,
    });
};

</script>
<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                Record Cash In / Cash Out
            </h2>
        </template>
        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <Link
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            :href="route('finances.index')">
                        Back
                        </Link>
                    </div>
                </div>
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-ghost-white noto-sans border-b border-gray-200">
                        <form name="createForm" @submit.prevent="submit">
                            <div className="flex flex-col">
                                <div className="flex flex-row w-full figtree mb-4 space-x-4">
                                    <div class="relative w-3/12">
                                        <BreezeLabel for="amount" value="Amount" />

                                        <BreezeTextInput id="amount" type="input" class="mt-1 px-2 py-1 block w-full"
                                            v-model="form.amount" autofocus autocomplete=off />
                                            <span class="error-message text-red-600 text-sm absolute top-full left-0"
                                            v-if="isNaN(form.amount) || form.amount <= 0 || form.amount == ''"
                                            v-html="isNaN(form.amount)
                                            ? 'Invalid: Enter a numeric value'
                                            : form.amount == ''
                                            ? 'Invalid: Enter a value'
                                            : form.amount <= 0
                                            ? 'Invalid: Cash amount must not be less than <br> or equal to 0'
                                            : ''">
                                        </span>
                                    </div>
                                    <div class="relative w-5/12">
                                        <BreezeLabel for="referenceNumber" value="Reference Number" />

                                        <BreezeTextInput id="referenceNumber" type="input"
                                            class="mt-1 px-2 py-1 block w-full" v-model="form.referenceNumber"
                                            autocomplete=off />
                                            <span class="error-message text-red-600 text-sm absolute top-full left-0"
                                            v-html="form.referenceNumber == ''
                                            ? 'Invalid: Enter a value'
                                            : props.cashflows.find((log) => log.referenceNumber == form.referenceNumber)
                                            ? 'Invalid: Existing serial number'
                                            : ''">
                                        </span>
                                    </div>

                                    <div class="w-4/12">
                                        <BreezeLabel for="transactionType" value="Transaction Type" />

                                        <!-- Add a dropdown for Expense/Deposit -->
                                        <select id="transactionType" v-model="form.transactionType"
                                            class="mt-1 px-2 py-1 block w-full border rounded-md">
                                            <option value="Deposit">Deposit</option>
                                            <option value="Expense">Business Expense</option>
                                            <option value="Disbursement">Disbursement</option>
                                            <option value="Cash In">Cash In</option>
                                            <option value="Cash Out">Cash Out</option>
                                        </select>

                                        <!-- Display any validation errors for the transactionType -->
                                        <span class="text-red-600" v-if="form.errors.transactionType">
                                            {{ form.errors.transactionType }}
                                        </span>
                                    </div>
                                    <!-- ... existing code -->
                                </div>
                                <div className="flex flex-row w-full mb-4 mt-6 space-x-4">
                                    <div className="w-2/4">
                                        <BreezeLabel for="generalNotes" value="General Notes" />
                                        <BreezeTextInput id="generalNotes" type="input" class="mt-1 px-2 py-1 block w-full comments"
                                            v-model="form.generalNotes" autocomplete=off />
                                        <span className="text-red-600" v-if="form.errors.generalNotes">
                                            {{ form.errors.generalNotes }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div className="mt-4">
                                <button type="submit"
                                    className="px-6 py-2 font-bold font-montserrat text-white bg-dark-pastel-green rounded-md hover:bg-emerald transition-all duration-300 ease-in-out">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
.comments {
    height: 75px;
    resize: none;
}

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