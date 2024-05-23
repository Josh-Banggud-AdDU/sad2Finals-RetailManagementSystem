<script setup>

import { ref, defineEmits, onMounted } from 'vue';

const confirmHovered = ref(false);
const cancelHovered = ref(false);
const emit = defineEmits();
const closeModal = () => {
    emit("close");
    confirmHovered.value = false;
    cancelHovered.value = false;
}
const confirmAction = () => {
    emit("confirmSubmission");
}
</script>

<template>
    <div class="fixed inset-0 bg-black opacity-50"></div>
    <div class="flex justify-center items-center h-screen z-50">
        <div class="w-full">
            <div class="mx-auto rounded-lg overflow-hidden bg-ghost-white">
                <div class="bg-saffron text-white p-4">
                    <h2 class="text-2xl font-bold font-montserrat text-center">Confirm Exit</h2>
                </div>
                <div class="flex p-3 px-8 pt-4 pb-6 flex-col figtree">
                    <div class="mb-2 text-lg font-montserrat text-justify leading-6">
                        Are you sure you want to exit from this page?
                    </div>
                    <div class="flex justify-center space-x-6 items-center pt-3">
                        <button type="button" @click="confirmAction" class="btn border border-saffron bg-saffron text-white rounded-md font-montserrat font-bold px-6 py-2 hover:bg-ecru hover:border-ecru transition-all duration-300 ease-in-out">
                            Confirm
                        </button>
                        <button type="button" @click="closeModal" class="btn border border-saffron bg-ghost-white text-saffron rounded-md font-montserrat font-bold px-6 py-2 hover:bg-ecru hover:border-ecru hover:text-ghost-white transition-all duration-300 ease-in-out">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-black {
    background-color: black;
    position: fixed;
    z-index: 40;
    /* Ensure the dimmed background is behind the modal (z-index 50) */
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.opacity-50 {
    opacity: 0.5;
}
</style>