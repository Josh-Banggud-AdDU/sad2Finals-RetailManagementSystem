<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeLabel from '@/Components/InputLabel.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import WarningIncompleteModal from '@/Components/WarningIncompleteModal.vue';
import ConfirmInputModal from '@/Components/ConfirmInputModal.vue';
import ConfirmBackModal from '@/Components/ConfirmBackModal.vue';
import { Head, Link, useForm, defineProps } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref, reactive } from 'vue';
import axios from 'axios';

const props = defineProps({
    cashOnHand: Array
})

onMounted(() => {
    console.log(props.cashOnHand)
})

const form = useForm({

});

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

const finalForm = useForm({
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
    oneCentavos: '',
    type: 'day_out'
});

const isVisible = ref(false);

const data = reactive({
    editable: false,
    thousandsTens: Math.floor(props.cashOnHand.thousands / 10),
    thousandsOnes: props.cashOnHand.thousands % 10,
    thousandsTotal: (parseFloat(props.cashOnHand.thousands) * cashValues[0]).toFixed(2),
    fiveHundredsTens: Math.floor(props.cashOnHand.fiveHundreds / 10),
    fiveHundredsOnes: props.cashOnHand.fiveHundreds % 10,
    fiveHundredsTotal: (parseFloat(props.cashOnHand.fiveHundreds) * cashValues[1]).toFixed(2),
    twoHundredsTens: Math.floor(props.cashOnHand.twoHundreds / 10),
    twoHundredsOnes: props.cashOnHand.twoHundreds % 10,
    twoHundredsTotal: (parseFloat(props.cashOnHand.twoHundreds) * cashValues[2]).toFixed(2),
    oneHundredsTens: Math.floor(props.cashOnHand.oneHundreds / 10),
    oneHundredsOnes: props.cashOnHand.oneHundreds % 10,
    oneHundredsTotal: (parseFloat(props.cashOnHand.oneHundreds) * cashValues[3]).toFixed(2),
    fiftiesTens: Math.floor(props.cashOnHand.fifties / 10),
    fiftiesOnes: props.cashOnHand.fifties % 10,
    fiftiesTotal: (parseFloat(props.cashOnHand.fifties) * cashValues[4]).toFixed(2),
    twentiesTens: Math.floor(props.cashOnHand.twenties / 10),
    twentiesOnes: props.cashOnHand.twenties % 10,
    twentiesTotal: (parseFloat(props.cashOnHand.twenties) * cashValues[5]).toFixed(2),
    tensTens: Math.floor(props.cashOnHand.tens / 10),
    tensOnes: props.cashOnHand.tens % 10,
    tensTotal: (parseFloat(props.cashOnHand.tens) * cashValues[6]).toFixed(2),
    fivesTens: Math.floor(props.cashOnHand.fives / 10),
    fivesOnes: props.cashOnHand.fives % 10,
    fivesTotal: (parseFloat(props.cashOnHand.fives) * cashValues[7]).toFixed(2),
    onesTens: Math.floor(props.cashOnHand.ones / 10),
    onesOnes: props.cashOnHand.ones % 10,
    onesTotal: (parseFloat(props.cashOnHand.ones) * cashValues[8]).toFixed(2),
    twentyFiveCentavosTens: Math.floor(props.cashOnHand.twentyFiveCentavos / 10),
    twentyFiveCentavosOnes: props.cashOnHand.twentyFiveCentavos % 10,
    twentyFiveCentavosTotal: (parseFloat(props.cashOnHand.twentyFiveCentavos) * cashValues[9]).toFixed(2),
    tenCentavosTens: Math.floor(props.cashOnHand.tenCentavos / 10),
    tenCentavosOnes: props.cashOnHand.tenCentavos % 10,
    tenCentavosTotal: (parseFloat(props.cashOnHand.tenCentavos) * cashValues[10]).toFixed(2),
    fiveCentavosTens: Math.floor(props.cashOnHand.fiveCentavos / 10),
    fiveCentavosOnes: props.cashOnHand.fiveCentavos % 10,
    fiveCentavosTotal: (parseFloat(props.cashOnHand.fiveCentavos) * parseFloat(cashValues[11])).toFixed(2),
    oneCentavosTens: Math.floor(props.cashOnHand.oneCentavos / 10),
    oneCentavosOnes: props.cashOnHand.oneCentavos % 10,
    oneCentavosTotal: (parseFloat(props.cashOnHand.oneCentavos) * cashValues[12]).toFixed(2),
});

const initialForm = useForm({
    thousandsTens: data.thousandsTens,
    thousandsOnes: data.thousandsOnes,
    thousandsTotal: data.thousandsTotal,
    fiveHundredsTens: data.fiveHundredsTens,
    fiveHundredsOnes: data.fiveHundredsOnes,
    fiveHundredsTotal: data.fiveHundredsTotal,
    twoHundredsTens: data.twoHundredsTens,
    twoHundredsOnes: data.twoHundredsOnes,
    twoHundredsTotal: data.twoHundredsTotal,
    oneHundredsTens: data.oneHundredsTens,
    oneHundredsOnes: data.oneHundredsOnes,
    oneHundredsTotal: data.oneHundredsTotal,
    fiftiesTens: data.fiftiesTens,
    fiftiesOnes: data.fiftiesOnes,
    fiftiesTotal: data.fiftiesTotal,
    twentiesTens: data.twentiesTens,
    twentiesOnes: data.twentiesOnes,
    twentiesTotal: data.twentiesTotal,
    tensTens: data.tensTens,
    tensOnes: data.tensOnes,
    tensTotal: data.tensTotal,
    fivesTens: data.fivesTens,
    fivesOnes: data.fivesOnes,
    fivesTotal: data.fivesTotal,
    onesTens: data.onesTens,
    onesOnes: data.onesOnes,
    onesTotal: data.onesTotal,
    twentyFiveCentavosTens: data.twentyFiveCentavosTens,
    twentyFiveCentavosOnes: data.twentyFiveCentavosOnes,
    twentyFiveCentavosTotal: data.twentyFiveCentavosTotal,
    tenCentavosTens: data.tenCentavosTens,
    tenCentavosOnes: data.tenCentavosOnes,
    tenCentavosTotal: data.tenCentavosTotal,
    fiveCentavosTens: data.fiveCentavosTens,
    fiveCentavosOnes: data.fiveCentavosOnes,
    fiveCentavosTotal: data.fiveCentavosTotal,
    oneCentavosTens: data.oneCentavosTens,
    oneCentavosOnes: data.oneCentavosOnes,
    oneCentavosTotal: data.oneCentavosTotal,
    thousandsTensValid: true,
    thousandsOnesValid: true,
    fiveHundredsTensValid: true,
    fiveHundredsOnesValid: true,
    twoHundredsTensValid: true,
    twoHundredsOnesValid: true,
    oneHundredsTensValid: true,
    oneHundredsOnesValid: true,
    fiftiesTensValid: true,
    fiftiesOnesValid: true,
    twentiesTensValid: true,
    twentiesOnesValid: true,
    tensTensValid: true,
    tensOnesValid: true,
    fivesTensValid: true,
    fivesOnesValid: true,
    onesTensValid: true,
    onesOnesValid: true,
    twentyFiveCentavosTensValid: true,
    twentyFiveCentavosOnesValid: true,
    tenCentavosTensValid: true,
    tenCentavosOnesValid: true,
    fiveCentavosTensValid: true,
    fiveCentavosOnesValid: true,
    oneCentavosTensValid: true,
    oneCentavosOnesValid: true,
})

const finalize = () => {
    finalForm.thousands = (parseInt(initialForm.thousandsTens) * 10) + parseInt(initialForm.thousandsOnes);
    finalForm.fiveHundreds = (parseInt(initialForm.fiveHundredsTens) * 10) + parseInt(initialForm.fiveHundredsOnes);
    finalForm.twoHundreds = (parseInt(initialForm.twoHundredsTens) * 10) + parseInt(initialForm.twoHundredsOnes);
    finalForm.oneHundreds = (parseInt(initialForm.oneHundredsTens) * 10) + parseInt(initialForm.oneHundredsOnes);
    finalForm.fifties = (parseInt(initialForm.fiftiesTens) * 10) + parseInt(initialForm.fiftiesOnes);
    finalForm.twenties = (parseInt(initialForm.twentiesTens) * 10) + parseInt(initialForm.twentiesOnes);
    finalForm.tens = (parseInt(initialForm.tensTens) * 10) + parseInt(initialForm.tensOnes);
    finalForm.fives = (parseInt(initialForm.fivesTens) * 10) + parseInt(initialForm.fivesOnes);
    finalForm.ones = (parseInt(initialForm.onesTens) * 10) + parseInt(initialForm.onesOnes);
    finalForm.twentyFiveCentavos = (parseInt(initialForm.twentyFiveCentavosTens) * 10) + parseInt(initialForm.twentyFiveCentavosOnes);
    finalForm.tenCentavos = (parseInt(initialForm.tenCentavosTens) * 10) + parseInt(initialForm.tenCentavosOnes);
    finalForm.fiveCentavos = (parseInt(initialForm.fiveCentavosTens) * 10) + parseInt(initialForm.fiveCentavosOnes);
    finalForm.oneCentavos = (parseInt(initialForm.oneCentavosTens) * 10) + parseInt(initialForm.oneCentavosOnes);
}

/*
const submit = () => {
    finalize();
    finalForm.post(route('cash_on_hands.update'), {
        thousands: (parseInt(initialForm.thousandsTens) * 10) + parseInt(initialForm.thousandsOnes),
        fiveHundreds: finalForm.fiveHundreds,
        twoHundreds: finalForm.twoHundreds,
        oneHundreds: finalForm.oneHundreds,
        fifties: finalForm.fifties,
        twenties: finalForm.twenties,
        tens: finalForm.tens,
        fives: finalForm.fives,
        ones: finalForm.ones,
        twentyFiveCentavos: finalForm.twentyFiveCentavos,
        tenCentavos: finalForm.tenCentavos,
        fiveCentavos: finalForm.fiveCentavos,
        oneCentavos: finalForm.oneCentavos,
        type: 'day_out'
    });
}*/
const submit = () => {
    finalize();
    if(
        initialForm.thousandsTens == '' ||
        isNaN(initialForm.thousandsTens) ||
        initialForm.thousandsTens < 0 ||
        initialForm.thousandsOnes == '' ||
        isNaN(initialForm.thousandsOnes) ||
        initialForm.thousandsOnes < 0 ||
        initialForm.thousandsOnes >= 10 ||
        initialForm.fiveHundredsTens == '' ||
        isNaN(initialForm.fiveHundredsTens) ||
        initialForm.fiveHundredsTens < 0 ||
        initialForm.fiveHundredsOnes == '' ||
        isNaN(initialForm.fiveHundredsOnes) ||
        initialForm.fiveHundredsOnes < 0 ||
        initialForm.fiveHundredsOnes >= 10 ||
        initialForm.twoHundredsTens == '' ||
        isNaN(initialForm.twoHundredsTens) ||
        initialForm.twoHundredsTens < 0 ||
        initialForm.twoHundredsOnes == '' ||
        isNaN(initialForm.twoHundredsOnes) ||
        initialForm.twoHundredsOnes < 0 ||
        initialForm.twoHundredsOnes >= 10 ||
        initialForm.oneHundredsTens == '' ||
        isNaN(initialForm.oneHundredsTens) ||
        initialForm.oneHundredsTens < 0 ||
        initialForm.oneHundredsOnes == '' ||
        isNaN(initialForm.oneHundredsOnes) ||
        initialForm.oneHundredsOnes < 0 ||
        initialForm.oneHundredsOnes >= 10 ||
        initialForm.fiftiesTens == '' ||
        isNaN(initialForm.fiftiesTens) ||
        initialForm.fiftiesTens < 0 ||
        initialForm.fiftiesOnes == '' ||
        isNaN(initialForm.fiftiesOnes) ||
        initialForm.fiftiesOnes < 0 ||
        initialForm.fiftiesOnes >= 10 ||
        initialForm.twentiesTens == '' ||
        isNaN(initialForm.twentiesTens) ||
        initialForm.twentiesTens < 0 ||
        initialForm.twentiesOnes == '' ||
        isNaN(initialForm.twentiesOnes) ||
        initialForm.twentiesOnes < 0 ||
        initialForm.twentiesOnes >= 10 ||
        initialForm.tensTens == '' ||
        isNaN(initialForm.tensTens) ||
        initialForm.tensTens < 0 ||
        initialForm.tensOnes == '' ||
        isNaN(initialForm.tensOnes) ||
        initialForm.tensOnes < 0 ||
        initialForm.tensOnes >= 10 ||
        initialForm.onesTens == '' ||
        isNaN(initialForm.onesTens) ||
        initialForm.onesTens < 0 ||
        initialForm.onesOnes == '' ||
        isNaN(initialForm.onesOnes) ||
        initialForm.onesOnes < 0 ||
        initialForm.onesOnes >= 10 ||
        initialForm.twentyFiveCentavosTens == '' ||
        isNaN(initialForm.twentyFiveCentavosTens) ||
        initialForm.twentyFiveCentavosTens < 0 ||
        initialForm.twentyFiveCentavosOnes == '' ||
        isNaN(initialForm.twentyFiveCentavosOnes) ||
        initialForm.twentyFiveCentavosOnes < 0 ||
        initialForm.twentyFiveCentavosOnes >= 10 ||
        initialForm.tenCentavosTens == '' ||
        isNaN(initialForm.tenCentavosTens) ||
        initialForm.tenCentavosTens < 0 ||
        initialForm.tenCentavosOnes == '' ||
        isNaN(initialForm.tenCentavosOnes) ||
        initialForm.tenCentavosOnes < 0 ||
        initialForm.tenCentavosOnes >= 10 ||
        initialForm.fiveCentavosTens == '' ||
        isNaN(initialForm.fiveCentavosTens) ||
        initialForm.fiveCentavosTens < 0 ||
        initialForm.fiveCentavosOnes == '' ||
        isNaN(initialForm.fiveCentavosOnes) ||
        initialForm.fiveCentavosOnes < 0 ||
        initialForm.fiveCentavosOnes >= 10 ||
        initialForm.oneCentavosTens == '' ||
        isNaN(initialForm.oneCentavosTens) ||
        initialForm.oneCentavosTens < 0 ||
        initialForm.oneCentavosOnes == '' ||
        isNaN(initialForm.oneCentavosOnes) ||
        initialForm.oneCentavosOnes < 0 ||
        initialForm.oneCentavosOnes >= 10
    ){
        isVisible3.value = true;
        displayInvalid();
    }else{
        isVisible4.value = true;
    }
}

const displayInvalid = (event) => {
    const id = event ? event.target.id : null;
    if(id == "1000Tens"){
        if(initialForm.thousandsTens == ''){
            form.errors.thousandsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.thousandsTens)){
            form.errors.thousandsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.thousandsTens < 0){
            form.errors.thousandsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.thousandsTens = '';
        }
    }else if(id == "1000Ones"){
        if(initialForm.thousandsOnes == ''){
            form.errors.thousandsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.thousandsOnes)){
            form.errors.thousandsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.thousandsOnes < 0){
            form.errors.thousandsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.thousandsOnes >= 10){
            form.errors.thousandsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.thousandsOnes = '';
        }
    }else if(id == "500Tens"){
        if(initialForm.fiveHundredsTens == ''){
            form.errors.fiveHundredsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveHundredsTens)){
            form.errors.fiveHundredsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveHundredsTens < 0){
            form.errors.fiveHundredsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fiveHundredsTens = '';
        }
    }else if(id == "500Ones"){
        if(initialForm.fiveHundredsOnes == ''){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveHundredsOnes)){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveHundredsOnes < 0){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fiveHundredsOnes >= 10){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fiveHundredsOnes = '';
        }
    }else if(id == "200Tens"){
        if(initialForm.twoHundredsTens == ''){
            form.errors.twoHundredsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twoHundredsTens)){
            form.errors.twoHundredsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.twoHundredsTens < 0){
            form.errors.twoHundredsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.twoHundredsTens = '';
        }
    }else if(id == "200Ones"){
        if(initialForm.twoHundredsOnes == ''){
            form.errors.twoHundredsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twoHundredsOnes)){
            form.errors.twoHundredsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.twoHundredsOnes < 0){
            form.errors.twoHundredsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.twoHundredsOnes >= 10){
            form.errors.twoHundredsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.twoHundredsOnes = '';
        }
    }else if(id == "100Tens"){
        if(initialForm.oneHundredsTens == ''){
            form.errors.oneHundredsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneHundredsTens)){
            form.errors.oneHundredsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneHundredsTens < 0){
            form.errors.oneHundredsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.oneHundredsTens = '';
        }
    }else if(id == "100Ones"){
        if(initialForm.oneHundredsOnes == ''){
            form.errors.oneHundredsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneHundredsOnes)){
            form.errors.oneHundredsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneHundredsOnes < 0){
            form.errors.oneHundredsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.oneHundredsOnes >= 10){
            form.errors.oneHundredsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.oneHundredsOnes = '';
        }
    }else if(id == "50Tens"){
        if(initialForm.fiftiesTens == ''){
            form.errors.fiftiesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiftiesTens)){
            form.errors.fiftiesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiftiesTens < 0){
            form.errors.fiftiesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fiftiesTens = '';
        }
    }else if(id == "50Ones"){
        if(initialForm.fiftiesOnes == ''){
            form.errors.fiftiesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiftiesOnes)){
            form.errors.fiftiesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiftiesOnes < 0){
            form.errors.fiftiesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fiftiesOnes >= 10){
            form.errors.fiftiesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fiftiesOnes = '';
        }
    }else if(id == "20Tens"){
        if(initialForm.twentiesTens == ''){
            form.errors.twentiesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentiesTens)){
            form.errors.twentiesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentiesTens < 0){
            form.errors.twentiesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.twentiesTens = '';
        }
    }else if(id == "20Ones"){
        if(initialForm.twentiesOnes == ''){
            form.errors.twentiesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentiesOnes)){
            form.errors.twentiesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentiesOnes < 0){
            form.errors.twentiesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.twentiesOnes >= 10){
            form.errors.twentiesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.twentiesOnes = '';
        }
    }else if(id == "10Tens"){
        if(initialForm.tensTens == ''){
            form.errors.tensTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tensTens)){
            form.errors.tensTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.tensTens < 0){
            form.errors.tensTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.tensTens = '';
        }
    }else if(id == "10Ones"){
        if(initialForm.tensOnes == ''){
            form.errors.tensOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tensOnes)){
            form.errors.tensOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.tensOnes < 0){
            form.errors.tensOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.tensOnes >= 10){
            form.errors.tensOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.tensOnes = '';
        }
    }else if(id == "5Tens"){
        if(initialForm.fivesTens == ''){
            form.errors.fivesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fivesTens)){
            form.errors.fivesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fivesTens < 0){
            form.errors.fivesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fivesTens = '';
        }
    }else if(id == "5Ones"){
        if(initialForm.fivesOnes == ''){
            form.errors.fivesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fivesOnes)){
            form.errors.fivesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fivesOnes < 0){
            form.errors.fivesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fivesOnes >= 10){
            form.errors.fivesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fivesOnes = '';
        }
    }else if(id == "1Tens"){
        if(initialForm.onesTens == ''){
            form.errors.onesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.onesTens)){
            form.errors.onesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.onesTens < 0){
            form.errors.onesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.onesTens = '';
        }
    }else if(id == "1Ones"){
        if(initialForm.onesOnes == ''){
            form.errors.onesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.onesOnes)){
            form.errors.onesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.onesOnes < 0){
            form.errors.onesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.onesOnes >= 10){
            form.errors.onesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.onesOnes = '';
        }
    }else if(id == "25cTens"){
        if(initialForm.twentyFiveCentavosTens == ''){
            form.errors.twentyFiveCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentyFiveCentavosTens)){
            form.errors.twentyFiveCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentyFiveCentavosTens < 0){
            form.errors.twentyFiveCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.twentyFiveCentavosTens = '';
        }
    }else if(id == "25cOnes"){
        if(initialForm.twentyFiveCentavosOnes == ''){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentyFiveCentavosOnes)){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentyFiveCentavosOnes < 0){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.twentyFiveCentavosOnes >= 10){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.twentyFiveCentavosOnes = '';
        }
    }else if(id == "10cTens"){
        if(initialForm.tenCentavosTens == ''){
            form.errors.tenCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tenCentavosTens)){
            form.errors.tenCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.tenCentavosTens < 0){
            form.errors.tenCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.tenCentavosTens = '';
        }
    }else if(id == "10cOnes"){
        if(initialForm.tenCentavosOnes == ''){
            form.errors.tenCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tenCentavosOnes)){
            form.errors.tenCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.tenCentavosOnes < 0){
            form.errors.tenCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.tenCentavosOnes >= 10){
            form.errors.tenCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.tenCentavosOnes = '';
        }
    }else if(id == "5cTens"){
        if(initialForm.fiveCentavosTens == ''){
            form.errors.fiveCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveCentavosTens)){
            form.errors.fiveCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveCentavosTens < 0){
            form.errors.fiveCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fiveCentavosTens = '';
        }
    }else if(id == "5cOnes"){
        if(initialForm.fiveCentavosOnes == ''){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveCentavosOnes)){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveCentavosOnes < 0){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fiveCentavosOnes >= 10){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fiveCentavosOnes = '';
        }
    }else if(id == "1cTens"){
        if(initialForm.oneCentavosTens == ''){
            form.errors.oneCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneCentavosTens)){
            form.errors.oneCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneCentavosTens < 0){
            form.errors.oneCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.oneCentavosTens = '';
        }
    }else if(id == "1cOnes"){
        if(initialForm.oneCentavosOnes == ''){
            form.errors.oneCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneCentavosOnes)){
            form.errors.oneCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneCentavosOnes < 0){
            form.errors.oneCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.oneCentavosOnes >= 10){
            form.errors.oneCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.oneCentavosOnes = '';
        }
    }else if(id == null){
        if(initialForm.thousandsTens == ''){
            form.errors.thousandsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.thousandsTens)){
            form.errors.thousandsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.thousandsTens < 0){
            form.errors.thousandsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.thousandsTens = '';
        }
        if(initialForm.thousandsOnes == ''){
            form.errors.thousandsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.thousandsOnes)){
            form.errors.thousandsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.thousandsOnes < 0){
            form.errors.thousandsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.thousandsOnes >= 10){
            form.errors.thousandsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.thousandsOnes = '';
        }
        if(initialForm.fiveHundredsTens == ''){
            form.errors.fiveHundredsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveHundredsTens)){
            form.errors.fiveHundredsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveHundredsTens < 0){
            form.errors.fiveHundredsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fiveHundredsTens = '';
        }
        if(initialForm.twoHundredsTens == ''){
            form.errors.twoHundredsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twoHundredsTens)){
            form.errors.twoHundredsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.twoHundredsTens < 0){
            form.errors.twoHundredsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.twoHundredsTens = '';
        }
        if(initialForm.fiveHundredsOnes == ''){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveHundredsOnes)){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveHundredsOnes < 0){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fiveHundredsOnes >= 10){
            form.errors.fiveHundredsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fiveHundredsOnes = '';
        }
        if(initialForm.twoHundredsOnes == ''){
            form.errors.twoHundredsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twoHundredsOnes)){
            form.errors.twoHundredsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.twoHundredsOnes < 0){
            form.errors.twoHundredsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.twoHundredsOnes >= 10){
            form.errors.twoHundredsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.twoHundredsOnes = '';
        }
        if(initialForm.oneHundredsTens == ''){
            form.errors.oneHundredsTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneHundredsTens)){
            form.errors.oneHundredsTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneHundredsTens < 0){
            form.errors.oneHundredsTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.oneHundredsTens = '';
        }
        if(initialForm.oneHundredsOnes == ''){
            form.errors.oneHundredsOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneHundredsOnes)){
            form.errors.oneHundredsOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneHundredsOnes < 0){
            form.errors.oneHundredsOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.oneHundredsOnes >= 10){
            form.errors.oneHundredsOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.oneHundredsOnes = '';
        }
        if(initialForm.fiftiesTens == ''){
            form.errors.fiftiesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiftiesTens)){
            form.errors.fiftiesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiftiesTens < 0){
            form.errors.fiftiesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fiftiesTens = '';
        }
        if(initialForm.fiftiesOnes == ''){
            form.errors.fiftiesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiftiesOnes)){
            form.errors.fiftiesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiftiesOnes < 0){
            form.errors.fiftiesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fiftiesOnes >= 10){
            form.errors.fiftiesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fiftiesOnes = '';
        }
        if(initialForm.twentiesTens == ''){
            form.errors.twentiesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentiesTens)){
            form.errors.twentiesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentiesTens < 0){
            form.errors.twentiesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.twentiesTens = '';
        }
        if(initialForm.twentiesOnes == ''){
            form.errors.twentiesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentiesOnes)){
            form.errors.twentiesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentiesOnes < 0){
            form.errors.twentiesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.twentiesOnes >= 10){
            form.errors.twentiesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.twentiesOnes = '';
        }
        if(initialForm.tensTens == ''){
            form.errors.tensTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tensTens)){
            form.errors.tensTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.tensTens < 0){
            form.errors.tensTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.tensTens = '';
        }
        if(initialForm.tensOnes == ''){
            form.errors.tensOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tensOnes)){
            form.errors.tensOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.tensOnes < 0){
            form.errors.tensOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.tensOnes >= 10){
            form.errors.tensOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.tensOnes = '';
        }
        if(initialForm.fivesTens == ''){
            form.errors.fivesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fivesTens)){
            form.errors.fivesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fivesTens < 0){
            form.errors.fivesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fivesTens = '';
        }
        if(initialForm.fivesOnes == ''){
            form.errors.fivesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fivesOnes)){
            form.errors.fivesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fivesOnes < 0){
            form.errors.fivesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fivesOnes >= 10){
            form.errors.fivesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fivesOnes = '';
        }
        if(initialForm.onesTens == ''){
            form.errors.onesTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.onesTens)){
            form.errors.onesTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.onesTens < 0){
            form.errors.onesTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.onesTens = '';
        }
        if(initialForm.onesOnes == ''){
            form.errors.onesOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.onesOnes)){
            form.errors.onesOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.onesOnes < 0){
            form.errors.onesOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.onesOnes >= 10){
            form.errors.onesOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.onesOnes = '';
        }
        if(initialForm.twentyFiveCentavosTens == ''){
            form.errors.twentyFiveCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentyFiveCentavosTens)){
            form.errors.twentyFiveCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentyFiveCentavosTens < 0){
            form.errors.twentyFiveCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.twentyFiveCentavosTens = '';
        }
        if(initialForm.twentyFiveCentavosOnes == ''){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.twentyFiveCentavosOnes)){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.twentyFiveCentavosOnes < 0){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.twentyFiveCentavosOnes >= 10){
            form.errors.twentyFiveCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.twentyFiveCentavosOnes = '';
        }
        if(initialForm.tenCentavosTens == ''){
            form.errors.tenCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tenCentavosTens)){
            form.errors.tenCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.tenCentavosTens < 0){
            form.errors.tenCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.tenCentavosTens = '';
        }
        if(initialForm.tenCentavosOnes == ''){
            form.errors.tenCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.tenCentavosOnes)){
            form.errors.tenCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.tenCentavosOnes < 0){
            form.errors.tenCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.tenCentavosOnes >= 10){
            form.errors.tenCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.tenCentavosOnes = '';
        }
        if(initialForm.fiveCentavosTens == ''){
            form.errors.fiveCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveCentavosTens)){
            form.errors.fiveCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveCentavosTens < 0){
            form.errors.fiveCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.fiveCentavosTens = '';
        }
        if(initialForm.fiveCentavosOnes == ''){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.fiveCentavosOnes)){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.fiveCentavosOnes < 0){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.fiveCentavosOnes >= 10){
            form.errors.fiveCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.fiveCentavosOnes = '';
        }
        if(initialForm.oneCentavosTens == ''){
            form.errors.oneCentavosTens = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneCentavosTens)){
            form.errors.oneCentavosTens = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneCentavosTens < 0){
            form.errors.oneCentavosTens = 'Invalid: Enter a value greater than or equal to 0';
        }else{
            form.errors.oneCentavosTens = '';
        }
        if(initialForm.oneCentavosOnes == ''){
            form.errors.oneCentavosOnes = 'Invalid: Enter a value';
        }else if(isNaN(initialForm.oneCentavosOnes)){
            form.errors.oneCentavosOnes = 'Invalid: Enter a numeric value';
        }else if(initialForm.oneCentavosOnes < 0){
            form.errors.oneCentavosOnes = 'Invalid: Enter a value greater than or equal to 0';
        }else if(initialForm.oneCentavosOnes >= 10){
            form.errors.oneCentavosOnes = 'Invalid: Enter a value less than 10';
        }else{
            form.errors.oneCentavosOnes = '';
        }
    }
}
const isVisible3 = ref(false);
const isVisible4 = ref(false);
const isVisible5 = ref(false);

const closeWarning = () => {
    isVisible3.value = false;
    isVisible4.value = false;
    isVisible5.value = false;
}

const confirm = () => {
    isVisible4.value = false;
    finalize();
    finalForm.post(route('cash_on_hands.update'), {
        thousands: (parseInt(initialForm.thousandsTens) * 10) + parseInt(initialForm.thousandsOnes),
        fiveHundreds: finalForm.fiveHundreds,
        twoHundreds: finalForm.twoHundreds,
        oneHundreds: finalForm.oneHundreds,
        fifties: finalForm.fifties,
        twenties: finalForm.twenties,
        tens: finalForm.tens,
        fives: finalForm.fives,
        ones: finalForm.ones,
        twentyFiveCentavos: finalForm.twentyFiveCentavos,
        tenCentavos: finalForm.tenCentavos,
        fiveCentavos: finalForm.fiveCentavos,
        oneCentavos: finalForm.oneCentavos,
        type: 'day_out'
    });
}

const exit = () => {
    isVisible5.value = false;
    Inertia.visit(route('cash_on_hands.index'));
}
</script>

<template>
    <Head title="Dashboard" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">
                Edit Cash on Hand
            </h2>
        </template>
        <div v-if="isVisible3" class="fixed inset-0 flex items-center justify-center z-50">
            <WarningIncompleteModal @close="closeWarning" />
        </div>
        <div v-if="isVisible4" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmInputModal @confirmSubmission="confirm" @close="closeWarning" />
        </div>
        <div v-if="isVisible5" class="fixed inset-0 flex items-center justify-center z-50">
            <ConfirmBackModal @confirmSubmission="exit" @close="closeWarning" />
        </div>
        <div class="py-6 w-full h-full flex flex-col gap-4">
            <div class="w-full flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <button type="button" @click="isVisible5 = true"
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            >
                            Back
                        </button>
                    </div>
                </div>
            </div>
            <div class="w-full flex flex-col space-y-4 max-w-7xl gap-4 mx-auto sm:px-6 lg:px-8">
                <form name="createForm" @submit.prevent="submit">
                    <table class="w-full bg-savoy-blue rounded-t-md">
                        <thead class="text-2xl text-white font-bold font-montserrat">
                            <tr>
                                <th class="px-2 py-2 w-2/6">Cash</th>
                                <th class="px-2 py-2 w-2/6">10 pcs</th>
                                <th class="px-2 py-2 w-2/6">1 pcs</th>
                            </tr>
                        </thead>
                    </table>
                    <table class = "w-full rounded-b-md bg-ghost-white">
                        <tbody class="text-center font-montserrat">
                            <!--onethousand-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[0]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="1000Tens" v-model="initialForm.thousandsTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.thousandsTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="1000Ones" v-model="initialForm.thousandsOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.thousandsOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--fivehundred-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[1]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="500Tens" v-model="initialForm.fiveHundredsTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fiveHundredsTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="500Ones" v-model="initialForm.fiveHundredsOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fiveHundredsOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--twohundred-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[2]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="200Tens" v-model="initialForm.twoHundredsTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.twoHundredsTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="200Ones" v-model="initialForm.twoHundredsOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.twoHundredsOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--onehundred-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[3]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="100Tens" v-model="initialForm.oneHundredsTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.oneHundredsTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="100Ones" v-model="initialForm.oneHundredsOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.oneHundredsOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--fifty-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[4]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="50Tens" v-model="initialForm.fiftiesTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fiftiesTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="50Ones" v-model="initialForm.fiftiesOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fiftiesOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--twenty-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[5]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="20Tens" v-model="initialForm.twentiesTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.twentiesTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="20Ones" v-model="initialForm.twentiesOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.twentiesOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--ten-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[6]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="10Tens" v-model="initialForm.tensTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.tensTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="10Ones" v-model="initialForm.tensOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.tensOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--five-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[7]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="5Tens" v-model="initialForm.fivesTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fivesTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="5Ones" v-model="initialForm.fivesOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fivesOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--one-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[8]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="1Tens" v-model="initialForm.onesTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.onesTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="1Ones" v-model="initialForm.onesOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.onesOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--twentyfivecents-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[9]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="25cTens" v-model="initialForm.twentyFiveCentavosTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.twentyFiveCentavosTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="25cOnes" v-model="initialForm.twentyFiveCentavosOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.twentyFiveCentavosOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--tencents-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[10]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="10cTens" v-model="initialForm.tenCentavosTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.tenCentavosTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="10cOnes" v-model="initialForm.tenCentavosOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.tenCentavosOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--fivecents-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[11]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="5cTens" v-model="initialForm.fiveCentavosTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fiveCentavosTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="5cOnes" v-model="initialForm.fiveCentavosOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.fiveCentavosOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                            <!--oncents-->
                            <tr>
                                <td class="text-xl tracking-wide text-right px-2 py-1 border-r font-bold border-silver w-2/6">{{
                                    parseFloat(cashValues[12]).toLocaleString('en-PH',
                                        {
                                            style: 'currency', currency: 'PHP'
                                        }) }}
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="1cTens" v-model="initialForm.oneCentavosTens" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.oneCentavosTens}}</div>
                                    </div>
                                </td>
                                <td class="border-r border-silver w-2/6">
                                    <div class="flex flex-row items-center justify-center px-1 ">
                                        <BreezeTextInput id="1cOnes" v-model="initialForm.oneCentavosOnes" type="input" @input="displayInvalid"
                                            class="text-lg text-center m-1 px-2 py-1 block w-3/6" autocomplete="off" />
                                            <div class="w-3/6 px-1 text-sm text-left text-persian-red">{{form.errors.oneCentavosOnes}}</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="w-full flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-ghost-white shadow-sm border-gray-200 sm:rounded-lg flex justify-end gap-4">
                <button type="button" @click="submit"
                    className="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue">
                    Save
                </button>
            </div>
        </div>
    </div>
</BreezeAuthenticatedLayout></template>

<style scoped></style>