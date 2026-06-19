<script setup lang="ts">
import { ref, onMounted } from 'vue'
import AIInsights from '@/components/ai/AIInsights.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { aiService } from '@/services/ai'
import type { AIInsights as AIInsightsType } from '@/types'

const insights = ref<AIInsightsType | null>(null)
const loading = ref(false)
const error = ref('')
const period = ref<'day' | 'week' | 'month'>('month')

async function loadInsights() {
  loading.value = true
  error.value = ''
  try {
    insights.value = await aiService.insights(period.value)
  } catch {
    error.value = 'Não foi possível carregar os insights. Verifique sua CLAUDE_API_KEY.'
  } finally {
    loading.value = false
  }
}

onMounted(loadInsights)
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold text-gray-900">IA & Insights</h1>
      <div class="flex items-center gap-2">
        <select
          v-model="period"
          class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
          @change="loadInsights"
        >
          <option value="day">Hoje</option>
          <option value="week">Esta semana</option>
          <option value="month">Este mês</option>
        </select>
        <button
          :disabled="loading"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
          @click="loadInsights"
        >
          Analisar
        </button>
      </div>
    </div>

    <div v-if="error" class="bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
      {{ error }}
    </div>

    <AIInsights :insights="insights" :loading="loading" />
  </div>
</template>
