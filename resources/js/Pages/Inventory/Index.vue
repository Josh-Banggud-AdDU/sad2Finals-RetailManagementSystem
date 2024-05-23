<script setup>
import BreezeAuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BreezeTextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/inertia-vue3';
import { ref, computed } from 'vue';
const props = defineProps({
    inventories: Array,
});
const form = useForm();
function destroy(id) {
    if (confirm("Are you sure you want to Delete")) {
        form.delete(route('inventories.destroy', id));
    }
}
const searchItem = ref("");
const itemSuggestions = ref([]);
const searchInventory = async () => {
  if (searchItem.value != "" && runInventorySearch.value == true) {
    const response = await fetch(`/search_inventories?searchItem=${searchItem.value}`);
    const data = await response.json();

    const uniqueItemNames = new Set();
    itemSuggestions.value = data.filter((suggestion) => {
      if (!uniqueItemNames.has(suggestion.itemName)) {
        uniqueItemNames.add(suggestion.itemName);
        return true;
      }
      return false;
    });
    
    if (itemSuggestions.value) {
        itemSuggestions.value = await itemSuggestions.value.slice(0, 6);
    }
  }
}
const selectItemSuggestion = (itemSuggestion) => {
  runInventorySearch.value = false;
  searchItem.value =itemSuggestion.itemName;
  clearItemSuggestions();
}
const clearItemSuggestions = () => {
  itemSuggestions.value = [];
}
const runInventorySearch = ref(true);
const resetRunSearch = () => {
    if (searchItem.value) {
        runInventorySearch.value = true;
    }
}
const filteredInventories = computed(() => {
    if (searchItem.value != "") {
        return props.inventories.filter((item) => item.itemName.toLowerCase().includes(searchItem.value.toLowerCase()));
    } else {
        console.log("Input field has no content");
        return props.inventories;
    }
});

</script>
<template>
    <Head title="Inventory" />
    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-savoy-blue leading-tight">Inventory</h2>
        </template>

        <div class="py-6">
            <div class="flex flex-col space-y-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="p-6 bg-white shadow-sm border-gray-200 sm:rounded-lg">
                    <div className="flex items-center justify-between">
                        <Link
                            class="px-6 py-2 text-white bg-savoy-blue font-bold font-montserrat rounded-md focus:outline-none hover:bg-vista-blue transition-all duration-300 ease-in-out"
                            :href="route('inventories.main')">
                        Back
                        </Link>
                    </div>
                </div>
                <div className="flex items-center">
                    <Link
                        className="w-full flex justify-center px-6 py-2 text-white bg-dark-pastel-green text-3xl font-black rounded-md focus:outline-none hover:bg-emerald transition-all duration-300 ease-in-out"
                        :href="route('inventories.create')">
                    +
                    </Link>
                </div>
                <div class=" relative rounded-md bg-ghost-white">
                    <div className="flex items-center m-1">
                        <font-awesome-icon :icon="['fas', 'magnifying-glass']" class="icon w-4 h-4 p-2" />
                        <BreezeTextInput id="searchInput" type="input"
                            class="px-2 py-1 block w-full bg-ghost-white shadow-none border-none focus:outline-none"
                            placeholder="Input Item Name" v-model="searchItem" @input="searchInventory" @click="resetRunSearch"
                            autocomplete="off">
                            <span className="text-red-600" v-if="form.errors.itemName">
                                {{ form.errors.itemName }}
                            </span>
                        </BreezeTextInput>
                    </div>
                    <ul v-if="searchItem && itemSuggestions.length"
                        className="absolute mt-2 w-full bg-ghost-white text-savoy-blue font-bold rounded-lg shadow-lg z-50 p-2">
                        <button type="button" v-for="suggestion in itemSuggestions" :key="suggestion.id"
                            @click="selectItemSuggestion(suggestion)" @mouseover="suggestion.hovered = true"
                            @mouseout="suggestion.hovered = false"
                            :class="{ 'bg-ghost-white text-savoy-blue': !suggestion.hovered, 'bg-silver': suggestion.hovered }"
                            class="pl-6 text-left w-full">
                            {{ suggestion.itemName }}
                        </button>
                    </ul>
                </div>
                <div style="max-height: calc(90vh - 16rem);" class="overflow-y-auto">
                    <div className="grid figtree grid-cols-2 gap-2 ">
                        <div v-for="item in filteredInventories" :key="item.id">
                            <Link :href="route('inventories.edit', item.id)">
                            <div
                                className="relative flex flex-col items-center w-full h-40 bg-ghost-white px-10 py-4 border-gray-200 rounded-lg hover:bg-silver transition-all duration-300 ease-in-out">
                                <div className="flex flex-row w-full h-9/12 z-50">
                                    <div className="w-10/12">
                                        <div class="font-bold text-xl line-clamp-2">{{ item.itemName }}</div>
                                    </div>
                                    <div className="w-1/12">
                                    </div>
                                    <div className="flex w-1/12">
                                        <span className="italic">{{ item.itemQuantity }} {{ item.itemUnit }}{{ "." }}</span>
                                    </div>
                                </div>
                                <div className="w-full italic text-sm h-1/12 z-50">
                                    <span>{{ item.itemSerialNumber }}</span>
                                </div>
                                <div className="w-full pt-1 text-lg h-1/12 line-clamp-1 z-50">
                                    <span>{{ item.itemCategory }}</span>
                                </div>
                                <div className="w-full text-m h-1/12 line-clamp-1 z-50">
                                    <span>{{ item.itemModel || 'N/A' }}</span>
                                </div>
                                <div class="absolute inset-0 opacity-75 rounded-lg"
                                    :class="{
                                        'bg-gradient-to-r from-light-coral from-10%': item.itemQuantity == 0,
                                        'bg-gradient-to-r from-saffron from-10%': item.itemQuantity < item.itemThreshold 
                                    }"></div>
                            </div>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </BreezeAuthenticatedLayout>
</template>

<style scoped>
.icon {
    color: #BFBFBF;
    /* Default icon color */
}

#searchInput::placeholder {
    font-style: italic;
    font-size: 0.9rem;
}

</style>

