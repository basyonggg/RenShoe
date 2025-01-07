<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const isAddressSettingOpen = ref(false); // Toggle accordion visibility

// Toggle accordion visibility
const toggleAccordion = () => {
    isAddressSettingOpen.value = !isAddressSettingOpen.value;
};

// Get user and address data from props
const { user, address } = usePage().props;

// Initialize form with user and address data
const form = useForm({
    username: user.username, // Bind username
    street: address?.street || '', // Ensure address fields are populated
    purok: address?.purok || '',
    barangay: address?.barangay || '',
    city: address?.city || '',
    zipcode: address?.zipcode || '',
});

</script>

<template>
    <form @submit.prevent="form.patch(route('profile.updateAdd'))" class="mt-6 space-y-6">
        <div class="bg-white shadow rounded">
            <!-- Accordion Toggle -->
            <button @click="toggleAccordion" class="w-full text-left px-4 py-3 font-bold text-gray-700 flex justify-between items-center">
                ADDRESS SETTING
                <span :class="{ 'rotate-180': isAddressSettingOpen }" class="transform transition-transform">&#x25BC;</span>
            </button>
            <input type="hidden" name="username" :value="form.username" />

            <!-- Accordion Content -->
            <div v-show="isAddressSettingOpen" class="px-4 py-6">
                <div class="w-full space-y-4">
                    <!-- Form Fields -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="street" value="Street" />
                            <TextInput id="street" v-model="form.street" type="text" class="w-full" />
                        </div>
                        <div>
                            <InputLabel for="purok" value="Purok" />
                            <TextInput id="purok" v-model="form.purok" type="text" class="w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="barangay" value="Barangay" />
                            <TextInput id="barangay" v-model="form.barangay" type="text" class="w-full" />
                        </div>
                        <div>
                            <InputLabel for="city" value="City" />
                            <TextInput id="city" v-model="form.city" type="text" class="w-full" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="zipcode" value="Zip Code" />
                            <TextInput id="zipcode" v-model="form.zipcode" type="text" class="w-full" />
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <PrimaryButton type="submit" :disabled="form.processing" class="w-48">Save Address</PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>
