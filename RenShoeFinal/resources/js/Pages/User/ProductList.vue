<script setup>
import UserLayouts from './Layouts/UserLayouts.vue';
import { ref, watch } from 'vue'
import {
    Dialog,
    DialogPanel,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import { XMarkIcon } from '@heroicons/vue/24/outline'
import { ChevronDownIcon, FunnelIcon, MinusIcon, PlusIcon, Squares2X2Icon } from '@heroicons/vue/20/solid'
import Products from '../User/Components/Products.vue'
import SecondaryButtonVue from '@/Components/SecondaryButton.vue';
import { router, useForm } from '@inertiajs/vue3';

const sortOptions = [
    { name: 'Most Popular', href: '#', current: true },
    { name: 'Best Rating', href: '#', current: false },
    { name: 'Newest', href: '#', current: false },
]

const filterPrices = useForm({
    prices: [0, 3000]
})

//Price filtering Method
const priceFilter = () => {
    filterPrices.transform((data) => ({
        ...data,
        prices: {
            from: filterPrices.prices[0],
            to: filterPrices.prices[1]
        }
    })).get('products', {
        preserveState: true,
        replace: true
    })
}

const mobileFiltersOpen = ref(false)

const props = defineProps({
    products: Array,
    brands: Array,
    categories: Array
})

// Category and Brand Filtering
const selectedBrands = ref([])
const selectedCategories = ref([])

watch(selectedBrands, () => {
    //Update the filtered products whenever selectedBrands change
    updateFilteredProducts()
})

watch(selectedCategories, () => {
    //Update the filtered products whenever selectedCategories change
    updateFilteredProducts()
})

function updateFilteredProducts (){
    router.get('products', {
        brands: selectedBrands.value,
        categories: selectedCategories.value
    },{
        preserveState: true,
        replace: true
    })
}

</script>

<template>

    <UserLayouts>
        <div class="bg-white">
            <div>
                <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex items-baseline justify-between border-b border-gray-200 pb-6 pt-24">
                        <h1 class="text-4xl font-bold tracking-tight text-gray-900">Featured Products</h1>

                        <div class="flex items-center">
                            <Menu as="div" class="relative inline-block text-left">
                                <div>
                                </div>

                                <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                    <MenuItems class="absolute right-0 z-10 mt-2 w-40 origin-top-right rounded-md bg-white shadow-2xl ring-1 ring-black/5 focus:outline-none">
                                        <div class="py-1">
                                            <MenuItem v-for="option in sortOptions" :key="option.name" v-slot="{ active }">
                                            <a :href="option.href" :class="[option.current ? 'font-medium text-gray-900' : 'text-gray-500', active ? 'bg-gray-100 outline-none' : '', 'block px-4 py-2 text-sm']">{{option.name }}</a>
                                            </MenuItem>
                                        </div>
                                    </MenuItems>
                                </transition>
                            </Menu>

                            <button type="button" class="-m-2 ml-4 p-2 text-gray-400 hover:text-gray-500 sm:ml-6 lg:hidden" @click="mobileFiltersOpen = true">
                                <span class="sr-only">Filters</span>
                                <FunnelIcon class="size-5" aria-hidden="true" />
                            </button>
                        </div>
                    </div>
                    <!--END OF MOBILE VIEW-->

                    <section aria-labelledby="products-heading" class="pb-24 pt-6">
                        <h2 id="products-heading" class="sr-only">Products</h2>
                        <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
                            <!-- Filters -->
                            <form class="hidden lg:block">
                                <h3 class="sr-only">Categories</h3>
                                <!--PRICE FILTER-->
                                <div class="flex items-center justify-between space-x-3">
                                    <div class="basis-1/3">
                                        <label for="filters-price-from" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">From</label>
                                        <input type="number" id="filters-price-from" placeholder="Min price" v-model="filterPrices.prices[0]" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" />
                                    </div>
                                    <div class="basis-1/3">
                                        <label for="filters-price-from" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">To</label>
                                        <input type="number" id="filters-price-from" placeholder="Max price" v-model="filterPrices.prices[1]" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" />
                                    </div>
                                    <SecondaryButtonVue class="self-end" @click="priceFilter()">Ok</SecondaryButtonVue>
                                </div>
                                <!--END PRICE FILTER-->

                                <!--BRANDS FILTER-->
                                <Disclosure as="div" class="border-b border-gray-200 py-6" v-slot="{ open }">
                                    <h3 class="-my-3 flow-root">
                                        <DisclosureButton class="flex w-full items-center justify-between bg-white py-3 text-sm text-gray-400 hover:text-gray-500">
                                            <span class="font-medium text-gray-900">Brands</span>
                                            <span class="ml-6 flex items-center">
                                                <PlusIcon v-if="!open" class="size-5" aria-hidden="true" />
                                                <MinusIcon v-else class="size-5" aria-hidden="true" />
                                            </span>
                                        </DisclosureButton>
                                    </h3>
                                    <DisclosurePanel class="pt-6">
                                        <div class="space-y-4">
                                            <div v-for="brand in brands" :key="brand.id" class="flex gap-3">
                                                <div class="flex h-5 shrink-0 items-center">
                                                    <div class="group grid size-4 grid-cols-1">
                                                        <input :id="`filter-${brand.id}`" :value="brand.id" type="checkbox" v-model="selectedBrands" class="col-start-1 row-start-1 appearance-none rounded border border-gray-300 bg-white checked:hover:bg-red-900 checked:hover:outline-red-900 focus-visible:active:bg-red-900 focus-visible:active:border-red-900 hover:border-black checked:border-black checked:bg-black indeterminate:border-black indeterminate:bg-black focus-visible:outline-black disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100" />
                                                        <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-[:disabled]:stroke-gray-950/25" viewBox="0 0 14 14" fill="none"> 
                                                            <path class="opacity-0 group-has-[:checked]:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path class="opacity-0 group-has-[:indeterminate]:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <label :for="`filter-${brand.id}`" class="text-sm text-gray-600">{{brand.name }}</label>
                                            </div>
                                        </div>
                                    </DisclosurePanel>
                                </Disclosure>
                                <!--END BRANDS FILTER-->

                                <!--CATEGORY FILTER-->
                                <Disclosure as="div" class="border-b border-gray-200 py-6" v-slot="{ open }">
                                    <h3 class="-my-3 flow-root">
                                        <DisclosureButton
                                            class="flex w-full items-center justify-between bg-white py-3 text-sm text-gray-400 hover:text-gray-500">
                                            <span class="font-medium text-gray-900">Categories</span>
                                            <span class="ml-6 flex items-center">
                                                <PlusIcon v-if="!open" class="size-5" aria-hidden="true" />
                                                <MinusIcon v-else class="size-5" aria-hidden="true" />
                                            </span>
                                        </DisclosureButton>
                                    </h3>
                                    <DisclosurePanel class="pt-6">
                                        <div class="space-y-4">
                                            <div v-for="category in categories" :key="category.id" class="flex gap-3">
                                                <div class="flex h-5 shrink-0 items-center">
                                                    <div class="group grid size-4 grid-cols-1">
                                                        <input :id="`filter-${category.id}`" :value="category.id" type="checkbox" v-model="selectedCategories" class="col-start-1 row-start-1 appearance-none rounded border border-gray-300 bg-white checked:hover:bg-red-900 checked:hover:outline-red-900 focus-visible:active:bg-red-900 focus-visible:active:border-red-900 hover:border-black checked:border-black checked:bg-black indeterminate:border-black indeterminate:bg-black focus-visible:outline-black disabled:border-gray-300 disabled:bg-gray-100 disabled:checked:bg-gray-100" />
                                                        <svg class="pointer-events-none col-start-1 row-start-1 size-3.5 self-center justify-self-center stroke-white group-has-[:disabled]:stroke-gray-950/25" viewBox="0 0 14 14" fill="none">
                                                            <path class="opacity-0 group-has-[:checked]:opacity-100" d="M3 8L6 11L11 3.5" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            <path class="opacity-0 group-has-[:indeterminate]:opacity-100" d="M3 7H11" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <label :for="`filter-${category.id}`" class="text-sm text-gray-600">{{category.name }}</label>
                                            </div>
                                        </div>
                                    </DisclosurePanel>
                                </Disclosure>
                                <!--END CATEGORY FILTER-->
                            </form>

                            <!-- Product grid -->
                            <div class="lg:col-span-3">
                                <!-- Your content -->
                                <Products :products="products.data"></Products>
                            </div>
                        </div>
                    </section>
                </main>
            </div>
        </div>


    </UserLayouts>



</template>