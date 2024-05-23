<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import AddSupplierModal from '@/Components/AddSupplierModal.vue';
import { Head, Link, useForm, defineProps } from '@inertiajs/inertia-vue3';
import { onMounted, ref, reactive } from 'vue';
import axios from 'axios';

const props = defineProps({
    cashOnHand: Array
})

onMounted(() => {
    console.log(props.cashOnHand)
})

const cashValues = reactive([
    '1000',
    '500',
    '200',
    '100',
    '50',
    '20',
    '10',
    '5',
    '1',
    '0.25',
    '0.10', 
    '0.05',
    '0.01',
]);

const form = useForm({
    thousands: '',
    fiveHundreds: '',
    twoHundreds: '',
    oneHundreds: '',
    fifties: '',
    twenties: '',
    tens: '',
    fives: '',
    ones: '',
    twentyFiveCentavos: '',
    tenCentavos: '',
    fiveCentavos: '',
    oneCentavos: ''
});

const isVisible = ref(false);

const currentDate = new Date().toISOString().slice(0, 10);

const filteredCashOnHand = props.cashOnHand.filter(entry => {
  const entryDate = new Date(entry.created_at).toISOString().slice(0, 10);
  return entryDate === currentDate;
});

const recordExist = filteredCashOnHand.length > 0;

const dayOut = ref(false);

if (filteredCashOnHand?.[filteredCashOnHand.length - 1]?.type == 'day_in')
{
    dayOut.value = true;
}
else
{
    dayOut.value
= false;
}

console.log(dayOut.value);

console.log(filteredCashOnHand?.[0]?.type);   

const data = reactive({
  editable: false,

  // Thousands
  thousandsTens: Math.floor(filteredCashOnHand?.[0]?.thousands / 10) || 0,
  thousandsOnes: filteredCashOnHand?.[0]?.thousands % 10 || 0,
  thousandsTotal: (filteredCashOnHand?.[0]?.thousands ?? 0) * cashValues[0],

  // Five Hundreds
  fiveHundredsTens: Math.floor(filteredCashOnHand?.[0]?.fiveHundreds / 10) || 0,
  fiveHundredsOnes: filteredCashOnHand?.[0]?.fiveHundreds % 10 || 0,
  fiveHundredsTotal: (filteredCashOnHand?.[0]?.fiveHundreds ?? 0) * cashValues[1],

  // Two Hundreds
  twoHundredsTens: Math.floor(filteredCashOnHand?.[0]?.twoHundreds / 10) || 0,
  twoHundredsOnes: filteredCashOnHand?.[0]?.twoHundreds % 10 || 0,
  twoHundredsTotal: (filteredCashOnHand?.[0]?.twoHundreds ?? 0) * cashValues[2],

  // One Hundreds
  oneHundredsTens: Math.floor(filteredCashOnHand?.[0]?.oneHundreds / 10) || 0,
  oneHundredsOnes: filteredCashOnHand?.[0]?.oneHundreds % 10 || 0,
  oneHundredsTotal: (filteredCashOnHand?.[0]?.oneHundreds ?? 0) * cashValues[3],

  // Fifties
  fiftiesTens: Math.floor(filteredCashOnHand?.[0]?.fifties / 10) || 0,
  fiftiesOnes: filteredCashOnHand?.[0]?.fifties % 10 || 0,
  fiftiesTotal: (filteredCashOnHand?.[0]?.fifties ?? 0) * cashValues[4],

  // Twenties
  twentiesTens: Math.floor(filteredCashOnHand?.[0]?.twenties / 10) || 0,
  twentiesOnes: filteredCashOnHand?.[0]?.twenties % 10 || 0,
  twentiesTotal: (filteredCashOnHand?.[0]?.twenties ?? 0) * cashValues[5],

  // Tens
  tensTens: Math.floor(filteredCashOnHand?.[0]?.tens / 10) || 0,
  tensOnes: filteredCashOnHand?.[0]?.tens % 10 || 0,
  tensTotal: (filteredCashOnHand?.[0]?.tens ?? 0) * cashValues[6],

  // Fives
  fivesTens: Math.floor(filteredCashOnHand?.[0]?.fives / 10) || 0,
  fivesOnes: filteredCashOnHand?.[0]?.fives % 10 || 0,
  fivesTotal: (filteredCashOnHand?.[0]?.fives ?? 0) * cashValues[7],

  // Ones
  onesTens: Math.floor(filteredCashOnHand?.[0]?.ones / 10) || 0,
  onesOnes: filteredCashOnHand?.[0]?.ones % 10 || 0,
  onesTotal: (filteredCashOnHand?.[0]?.ones ?? 0) * cashValues[8],

  // Twenty-Five Centavos
  twentyFiveCentavosTens: Math.floor(filteredCashOnHand?.[0]?.twentyFiveCentavos / 10) || 0,
  twentyFiveCentavosOnes: filteredCashOnHand?.[0]?.twentyFiveCentavos % 10 || 0,
  twentyFiveCentavosTotal: (filteredCashOnHand?.[0]?.twentyFiveCentavos ?? 0) * cashValues[9],

  // Ten Centavos
  tenCentavosTens: Math.floor(filteredCashOnHand?.[0]?.tenCentavos / 10) || 0,
  tenCentavosOnes: filteredCashOnHand?.[0]?.tenCentavos % 10 || 0,
  tenCentavosTotal: (filteredCashOnHand?.[0]?.tenCentavos ?? 0) * cashValues[10],

  // Five Centavos
  fiveCentavosTens: Math.floor(filteredCashOnHand?.[0]?.fiveCentavos / 10) || 0,
  fiveCentavosOnes: filteredCashOnHand?.[0]?.fiveCentavos % 10 || 0,
  fiveCentavosTotal: (filteredCashOnHand?.[0]?.fiveCentavos ?? 0) * cashValues[11],

  // One Centavos
  oneCentavosTens: Math.floor(filteredCashOnHand?.[0]?.oneCentavos / 10) || 0,
  oneCentavosOnes: filteredCashOnHand?.[0]?.oneCentavos % 10 || 0,
  oneCentavosTotal: (filteredCashOnHand?.[0]?.oneCentavos ?? 0) * cashValues[12],
});


</script>


<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                View Cash on Hand
            </h2>
        </template>
        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <Link
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            :href="route('finances.main')">
                        Back
                        </Link>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-col space-y-4 max-w-7xl gap-4 mx-auto sm:px-6 lg:px-8">
            <form name="createForm" @submit.prevent="submit">
            <table class="w-full bg-savoy-blue rounded-t-md">
                <thead class="text-2xl text-white font-bold font-montserrat">
                    <tr>
                        <th class="px-2 py-2 w-3/12" rowspan="2">Cash</th>
                        <th class="px-2 py-2 w-4/12" colspan="2">On Hand</th>
                        <th class="px-2 py-2 w-5/12" rowspan="2">Total</th>
                    </tr>
                    <tr>
                        <th class="text-xl w-2/12">10 (Pc/s)</th>
                        <th class="text-xl w-2/12">1 (Pc/s)</th>
                    </tr>
                </thead>
            </table>
            <table class = "w-full rounded-b-md bg-ghost-white">
                <tbody class= "text-center font-montserrat">
                    <!--onethousand-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[0]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.thousandsTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.thousandsOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.thousandsTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--fivehundred-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[1]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fiveHundredsTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fiveHundredsOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.fiveHundredsTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--twohundred-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[2]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.twoHundredsTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.twoHundredsOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.twoHundredsTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--onehundred-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[3]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.oneHundredsTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.oneHundredsOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.oneHundredsTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--fifty-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[4]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fiftiesTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fiftiesOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.fiftiesTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--twenty-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[5]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.twentiesTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.twentiesOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.twentiesTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--ten-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[6]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.tensTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.tensOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.tensTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--five-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[7]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fivesTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fivesOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.fivesTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--one-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[8]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.onesTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.onesOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.onesTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--twentyfivecents-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[9]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.twentyFiveCentavosTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.twentyFiveCentavosOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.twentyFiveCentavosTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--tencents-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[10]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.tenCentavosTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.tenCentavosOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.tenCentavosTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--fivecents-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[11]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fiveCentavosTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.fiveCentavosOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.fiveCentavosTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!--oncents-->
                    <tr>
                        <td class="text-xl tracking-wide text-right px-2 py-2 border-r border-silver w-3/12">{{ parseFloat(cashValues[12]).toLocaleString('en-PH',
                            {
                                style: 'currency', currency: 'PHP'
                            }) }}
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.oneCentavosTens}}
                                </div>
                            </div>
                        </td>
                        <td class="border-r border-silver w-2/12">
                            <div class="text-xl flex flex-col items-center justify-center px-2 py-2">
                                <div>
                                    {{data.oneCentavosOnes}}
                                </div>
                            </div>
                        </td>
                        <td class = "w-5/12">
                            <div class="text-xl tracking-wide text-right px-2 py-2 font-bold flex flex-col">
                                <div>
                                    {{ parseFloat(data.oneCentavosTotal).toLocaleString('en-PH',{style: 'currency', currency: 'PHP'}) }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg flex justify-end gap-4">
                    <Link
                        v-if="recordExist && dayOut" className="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue"
                        :href="route('cash_on_hands.end')">
                        Day Out
                    </Link>
                    <Link
                        v-if="!recordExist" className="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue"
                        :href="route('cash_on_hands.start')">
                        Day In
                    </Link>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>

</style>