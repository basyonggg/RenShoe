<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import UserLayouts from './Layouts/UserLayouts.vue';  
import { router as routerInertia } from "@inertiajs/vue3";

const router = useRouter();

defineProps({
  product: Object,
});

const addToCart = (product) => {
    routerInertia.post(route('cart.store', product), {
        onSuccess: (page) => {
            if (page.props.flash.success) {
                Swal.fire({
                    toast: true,
                    icon: "success",
                    position: "top-end",
                    showConfirmButton: false,
                    title: page.props.flash.success
                });
            }
        },
    })
}

const slides = ref([
  'Image 1', 
  'Image 2', 
  'Image 3', 
  'Image 4', 
  'Image 5'
]);

const currentSlide = ref(0);
const currentTab = ref('description'); // Tracks the current tab (description or reviews)
const quantity = ref(1); // Tracks the quantity of the product

const goBack = () => {
  router.back();
};

const changeSlide = (index) => {
  currentSlide.value = index;
};

const changeTab = (tab) => {
  currentTab.value = tab;
};

// Function to increase quantity
const increaseQuantity = () => {
  quantity.value++;
};

// Function to decrease quantity (with a minimum value of 1)
const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};
</script>

<template>
  <UserLayouts>
    <div class="max-w-7xl mx-auto px-6 py-8">
      <!-- Back Button -->
      <a href="/" @click.prevent="goBack" class="text-sm text-gray-500 hover:text-black">&larr; BACK</a>

      <!-- Product Details Section -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Left Side: Product Information -->
        <div>
          <h1 class="text-4xl font-bold">{{ product.product_name }}</h1>
            <p class="text-lg text-gray-500 mt-2">{{ product.brand.name }}</p>
            <p class="text-2xl font-semibold mt-4">${{ product.price }}</p>
                <div class="flex items-center mt-2">
                  <span class="text-yellow-500 text-lg font-bold">4.6</span>
                  <span class="ml-2 text-gray-600 text-sm">(NULL Reviews)</span>
                </div>
          <!-- Size Selection -->
          <div class="mt-6">
            <label for="size" class="block text-sm font-medium">Size</label>
            <select id="size" class="block mt-1 w-32 border border-gray-300 rounded px-3 py-2">
              <option>45</option>
              <option>44</option>
              <option>43</option>
            </select>
          </div>

          <!-- Quantity Selection -->
          <div class="mt-6">
            <label for="quantity" class="block text-sm font-medium">Quantity</label>
            <div class="flex items-center mt-1">
              <!-- Decrease Button -->
              <button @click="decreaseQuantity" class="w-10 h-10 border border-gray-300 text-lg font-semibold rounded-l">-</button>
              <input id="quantity" type="text" :value="quantity" readonly class="w-12 h-10 border-t border-b border-gray-300 text-center">
              <!-- Increase Button -->
              <button @click="increaseQuantity" class="w-10 h-10 border border-gray-300 text-lg font-semibold rounded-r">+</button>
            </div>
          </div> 

          <!-- Action Buttons -->
          <div class="mt-8 space-x-4">
            <a @click="addToCart(product)" class="border border-black text-black px-6 py-3 rounded hover:bg-black hover:text-white cursor-pointer">Add to Cart</a>
            <button class="bg-black text-white px-6 py-3 rounded hover:bg-gray-800">Buy Now</button>
          </div>
        </div>

        <!-- Right Side: Image Slider -->
          <div>
            <div id="slider" class="relative w-full h-[500px] overflow-hidden">
              <div class="absolute inset-0 flex transition-transform duration-500" :style="{ transform: 'translateX(' + (-currentSlide * 100) + '%)' }">
                <img v-for="image in product.product_images" :key="image.id" :src="`/${image.image}`" alt="Product Image" class="flex-none w-full h-full object-cover">
              </div>
            </div>
          <div class="flex justify-center mt-2 space-x-4">
          <button v-for="(image, index) in product.product_images" :key="index" @click="changeSlide(index)" :class="['w-2.5 h-2.5 rounded-full', currentSlide === index ? 'bg-black' : 'bg-gray-500']"></button>
        </div>
      </div>
    </div>

      <!-- Horizontal Line -->
      <hr class="border-gray-300 my-12">

      <!-- Tabs Section -->
      <div class="text-center">
        <div class="inline-flex space-x-8">
          <button @click="changeTab('description')" :class="{'text-black font-semibold border-b-2 border-black': currentTab === 'description'}" class="py-3 px-4 text-black hover:text-black">
            DESCRIPTION
          </button>
          <button @click="changeTab('reviews')" :class="{'text-black font-semibold border-b-2 border-black': currentTab === 'reviews'}" class="py-3 px-4 text-gray-500 hover:text-black">
            REVIEWS
          </button>
        </div>
      </div>

      <!-- Tab Content -->
      <div class="mt-6 text-center">
        <!-- Description Content -->
        <div v-if="currentTab === 'description'">
          <p class="text-gray-700">{{ product.description }}</p>
        </div>

        <!-- Reviews Content -->
        <div class="text-left" v-if="currentTab === 'reviews' ">
          <h2 class="text-lg font-bold mb-4" >4.6 Ratings</h2>
          <p class="text-gray-600 mb-6">NULL Reviews</p>

          <!-- Review Items -->
          <div>
            <div class="mb-6 text-left">
              <p class="font-bold">John Doe</p>
              <p class="text-gray-500 text-sm">12 days ago</p>
              <p class="mt-2">What is DBMS?</p>
            </div>
            <div class="mb-6 text-left">
              <p class="font-bold">Oscar Devs</p>
              <p class="text-gray-500 text-sm">12 days ago</p>
              <p class="mt-2">Gwapo kaayo sa picture, pag abot kay kanang tag 10 sa ukay'ukay</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </UserLayouts>
</template>

