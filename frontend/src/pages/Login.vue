<script setup lang="ts">
import { ref } from 'vue'
import { useAuth } from '@/composables/useAuth'

const { login } = useAuth()
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''
  try {
    await login(email.value, password.value)
  } catch {
    error.value = 'Email ou senha inválidos.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
      <div class="text-center mb-8">
        <div class="w-12 h-12 bg-blue-600 rounded-xl mx-auto mb-4 flex items-center justify-center">
          <span class="text-white font-bold text-lg">FA</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Financial Analyzer</h1>
        <p class="text-gray-500 text-sm mt-1">Gerencie suas finanças com IA</p>
      </div>

      <form class="space-y-4" @submit.prevent="handleLogin">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input
            v-model="email"
            type="email"
            required
            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="demo@financial-analyzer.com"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
          <input
            v-model="password"
            type="password"
            required
            class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
            placeholder="password123"
          />
        </div>

        <p v-if="error" class="text-sm text-red-600">{{ error }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 disabled:opacity-50 transition-colors"
        >
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>

      <div class="mt-6 p-3 bg-gray-50 rounded-lg">
        <p class="text-xs text-gray-500 font-medium mb-1">Demo:</p>
        <p class="text-xs text-gray-600">Email: demo@financial-analyzer.com</p>
        <p class="text-xs text-gray-600">Senha: password123</p>
      </div>
    </div>
  </div>
</template>
