<script setup lang="ts">
import { ref } from 'vue';
import { Delete } from 'lucide-vue-next';

const props = defineProps<{
    modelValue: string;
}>();

const emit = defineEmits<{
    'update:modelValue': [value: string];
    'submit': [];
}>();

const isShift = ref(true); // Start with capital letter

const rows = [
    ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'],
    ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p'],
    ['a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l'],
    ['z', 'x', 'c', 'v', 'b', 'n', 'm']
];

const handleKeyClick = (key: string) => {
    const char = isShift.value ? key.toUpperCase() : key;
    emit('update:modelValue', props.modelValue + char);
    
    // Auto-disable shift after first letter if it was just for caps lock
    if (isShift.value && props.modelValue.length === 0) {
        isShift.value = false;
    }
};

const handleBackspace = () => {
    emit('update:modelValue', props.modelValue.slice(0, -1));
};

const handleSpace = () => {
    emit('update:modelValue', props.modelValue + ' ');
};
</script>

<template>
    <div class="bg-gray-900 p-4 rounded-xl border border-gray-800 w-full select-none">
        <div class="flex flex-col gap-3">
            <!-- Normal Rows -->
            <div v-for="(row, rowIndex) in rows" :key="rowIndex" class="flex justify-center gap-2">
                <!-- Shift Key (only on Z row) -->
                <button 
                    v-if="rowIndex === 3"
                    @click="isShift = !isShift"
                    class="h-16 px-6 rounded-lg font-bold text-2xl uppercase transition-colors"
                    :class="isShift ? 'bg-white text-black' : 'bg-gray-800 text-white hover:bg-gray-700'"
                >
                    Shift
                </button>

                <!-- Letter/Number Keys -->
                <button 
                    v-for="key in row" 
                    :key="key"
                    @click="handleKeyClick(key)"
                    class="h-16 w-12 sm:w-16 rounded-lg bg-gray-800 text-white font-bold text-3xl uppercase hover:bg-gray-700 active:bg-gray-600 transition-colors flex items-center justify-center"
                >
                    {{ isShift ? key.toUpperCase() : key }}
                </button>

                <!-- Backspace Key (only on Z row) -->
                <button 
                    v-if="rowIndex === 3"
                    @click="handleBackspace"
                    class="h-16 px-6 rounded-lg bg-gray-800 text-white hover:bg-gray-700 active:bg-gray-600 transition-colors flex items-center justify-center"
                >
                    <Delete class="w-8 h-8" />
                </button>
            </div>

            <!-- Bottom Row: Spacebar -->
            <div class="flex justify-center gap-2">
                <button 
                    @click="handleSpace"
                    class="h-16 w-1/2 rounded-lg bg-gray-800 hover:bg-gray-700 active:bg-gray-600 transition-colors"
                ></button>
            </div>
        </div>
    </div>
</template>
