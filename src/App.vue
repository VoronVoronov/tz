<template>
  <div class="min-h-screen">
    <nav class="bg-white shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <router-link 
              to="/" 
              class="flex items-center px-4 py-2 text-gray-700 hover:text-gray-900"
            >
              Canvas Templates
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view v-slot="{ Component }">
        <transition 
          name="fade" 
          mode="out-in"
          enter-active-class="transition-opacity duration-200"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity duration-200"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <!-- Global Error Toast -->
    <div 
      v-if="errorStore.message"
      class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg"
      role="alert"
    >
      {{ errorStore.message }}
      <button 
        @click="errorStore.clearError"
        class="ml-3 text-white hover:text-red-100"
      >
        ×
      </button>
    </div>
  </div>
</template>

<script setup>
import { useErrorStore } from './stores/error'

const errorStore = useErrorStore()
</script>
