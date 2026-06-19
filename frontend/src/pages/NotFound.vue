<script setup lang="ts">
import { useRouter } from 'vue-router'
const router = useRouter()
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="text-center">
      <h1 class="text-6xl font-bold text-gray-300">404</h1>
      <p class="text-gray-500 mt-2 mb-6">Página não encontrada</p>
      <button
        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
        @click="router.push('/dashboard')"
      >
        Voltar ao Dashboard
      </button>
    </div>
  </div>
</template>
