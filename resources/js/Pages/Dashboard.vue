<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    inventories: Array,
    sales: Array,
    jobOrders: Array,
    suppliers: Array
})

const filteredNoStock = computed(() => {
    return props.inventories.filter((item) => item.itemQuantity == 0);
});

const filteredLowStock = computed(() => {
    return props.inventories.filter((item) => item.itemQuantity < item.itemThreshold && item.itemQuantity !== 0);
})

const filterJO = computed(() => {
    const numberOf = props.jobOrders.filter((jo) => jo.jobOrderStatus == 'Ongoing').length;
    return numberOf;
})

const filterSales = computed(() => {
    const today = new Date().toISOString().split('T')[0];
    console.log(today);
    const numberOf = props.sales.filter((sale) => sale.transactionDate === today).length;
    return numberOf;
})
</script>

<template>
    <Head title="Dashboard" />

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">Dashboard</h2>
        </template>

        <div class="py-6 grow">
            <div class="h-full flex flex-col justify-between max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!--Low Stock Preview-->
                <div class="h-[812px] flex flex-row gap-4 w-full">
                    <div class="h-full flex flex-col w-1/2">
                        <div class="h-12 w-full text-center bg-persian-red rounded-md px-2 py-2 mb-2">
                            <span class="font-montserrat font-bold text-3xl text-ghost-white uppercase">No Supply:</span>
                        </div>  
                        <div class="h-full overflow-y-auto">
                            <div v-for="item in filteredNoStock" :key="item.id">
                                <div
                                    class="relative flex flex-col items-center w-full h-32 bg-ghost-white px-10 py-4 border-gray-200 rounded-lg mb-3">
                                    <div class="flex flex-row w-full h-9/12 z-50">
                                        <div class="w-10/12">
                                            <div class="font-bold text-xl line-clamp-2">{{ item.itemName }}</div>
                                        </div>
                                        <div class="w-1/12"></div>
                                        <div class="flex w-1/12">
                                            <span class="italic">{{ item.itemQuantity }} {{ item.itemUnit }}{{ "."
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="w-full italic text-sm h-1/12 z-50">
                                        <span>{{ item.itemSerialNumber }}</span>
                                    </div>
                                    <div class="w-full text-m h-1/12 line-clamp-1 z-50">
                                        <span>{{ props.suppliers.find((supplier) => supplier.id ==
                                            item.supplierID)?.supplierName }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-1/2 bg-vista-blue" style="height: 710px;">
                        <div class="h-12 text-center bg-saffron rounded-md px-2 py-2 mb-2">
                            <span class="font-montserrat font-bold text-3xl text-ghost-white uppercase">Low Supply:</span>
                        </div>
                        <div class="flex flex-col space-y-2">
                            <div style="max-height: calc(90vh - 16rem);" class="overflow-y-auto ">
                                <div v-for="item in filteredLowStock" :key="item.id">
                                    <div
                                        class="relative flex flex-col items-center w-full h-32 bg-ghost-white px-10 py-4 border-gray-200 rounded-lg mb-3">
                                        <div class="flex flex-row w-full h-9/12 z-50">
                                            <div class="w-10/12">
                                                <div class="font-bold text-xl line-clamp-2">{{ item.itemName }}</div>
                                            </div>
                                            <div class="w-1/12"></div>
                                            <div class="flex w-1/12">
                                                <span class="italic">{{ item.itemQuantity }} {{ item.itemUnit }}{{ "."
                                                }}</span>
                                            </div>
                                        </div>
                                        <div class="w-full italic text-sm h-1/12 z-50">
                                            <span>{{ item.itemSerialNumber }}</span>
                                        </div>
                                        <div class="w-full text-m h-1/12 line-clamp-1 z-50">
                                            <span>{{ props.suppliers.find((supplier) => supplier.id ==
                                                item.supplierID)?.supplierName }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex">
                    <!--Finance Preview-->
                    <table class="bg-ghost-white w-full rounded-md">
                        <thead class="font-bold font-montserrat">
                            <tr class="p-2">
                                <th class="w-1/12"> </th>
                                <th class="w-3/12 py-6 px-4 text-savoy-blue text-right justify-right">
                                    <span class="text-3xl uppercase">Ongoing Job Orders</span>
                                </th>
                                <th class="w-2/12 py-6 px-4 text-5xl text-savoy-blue text-left">{{filterJO}}</th>
                                <th class="w-3/12 py-6 px-4 text-savoy-blue text-right justify-right">
                                    <span class="text-3xl uppercase">Today's Sales Orders</span>
                                </th>
                                <th class="w-2/12 py-6 px-4 text-5xl text-savoy-blue text-left">{{filterSales}}</th>
                                <th class="w-1/12"> </th>
                            </tr>
                        </thead>
                    </table>
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
    height: 300px;
    overflow-y: auto;
}

.scrollable-2 {
    height: 385px;
    overflow-y: auto;
}

.newscrollable {
    width: 1008px;
    overflow-x: auto;
}

.scrollable-table {
    height: 295px;
    /* Adjusted height */
    overflow-y: auto;
}

.scrollable-table-b {
    height: 295x;
    /* Adjusted height */
    overflow-y: auto;
}

.comments {
    height: 85px;
    resize: none;
}

.comments-2 {
    height: 50px;
    resize: none;
}

/* Adjust the style for the sticky header */
thead.sticky tr {
    position: sticky;
    top: 0;
    z-index: 1;
}</style>

