<script setup>
import { computed, onMounted, onUnmounted, ref, defineEmits } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'center',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white',
    },
    choices: {
        type: Array,
        required: true
    }
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'origin-top-left left-0';
    } else if (props.align === 'right') {
        return 'origin-top-right right-0';
    } else if(props.align === 'center'){
        return 'origin-top inset-x-0';
    }
});

const open = ref(false);

//Added code for choices
const emits = defineEmits(['choice-selected']);

const selectChoice = (choice) => {
    emits('choice-selected', choice); // Emit the choice-selected event
    open.value = false;
};
</script>

<template>
    <div class="relative">
        <div @click="open = !open">
            <slot name="trigger" />
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div id="dropdownOverlay" ref="dropdownOverlayRef" v-show="open" class="fixed inset-0 z-40" @click="open = false"></div>

        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95 transform origin-top"
            enter-to-class="opacity-100 scale-100 transform origin-top"
            leave-active-class="transition ease-in duration-75" 
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 w-full mt-2 rounded-md shadow-xl"
                :class="[alignmentClasses]"
            >
                <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                    <slot name="content">
                        <ul class="p-2">
                            <!-- Loop through your choices -->
                            <li v-for="(choice, index) in choices" :key="index">
                                <a href="#" @click="selectChoice(choice)">{{ choice }}</a>
                            </li>
                        </ul>
                    </slot>
                </div>
            </div>
        </Transition>
    </div>
</template>
