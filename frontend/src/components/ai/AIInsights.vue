<script setup lang="ts">
import { SparklesIcon, ExclamationTriangleIcon, LightBulbIcon } from '@heroicons/vue/24/outline'
import { formatCurrency } from '@/utils/currency'
import type { AIInsights } from '@/types'

defineProps<{
  insights: AIInsights | null
  loading?: boolean
}>()

const anomalyColors: Record<string, string> = {
  high: 'bg-red-50 border-red-200 text-red-700',
  medium: 'bg-yellow-50 border-yellow-200 text-yellow-700',
  low: 'bg-blue-50 border-blue-200 text-blue-700',
}
</script>

<template>
  <div class="space-y-4">
    <div v-if="loading" class="text-center py-8 text-gray-400">
      <SparklesIcon class="w-8 h-8 mx-auto mb-2 animate-pulse text-blue-400" />
      <p class="text-sm">Analisando com IA...</p>
    </div>

    <template v-else-if="insights">
      <!-- Anomalias -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-2 mb-3">
          <ExclamationTriangleIcon class="w-5 h-5 text-orange-500" />
          <h3 class="font-semibold text-gray-800">Anomalias Detectadas</h3>
          <span class="ml-auto text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full">
            {{ insights.anomalies.length }}
          </span>
        </div>
        <div v-if="insights.anomalies.length" class="space-y-2">
          <div
            v-for="anomaly in insights.anomalies"
            :key="anomaly.transaction_id"
            :class="['rounded-lg border p-3', anomalyColors[anomaly.severity]]"
          >
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm font-medium">{{ anomaly.description }}</span>
              <span class="text-sm font-bold">{{ formatCurrency(anomaly.amount) }}</span>
            </div>
            <p class="text-xs opacity-80">{{ anomaly.reason }}</p>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500">Nenhuma anomalia detectada este mês.</p>
      </div>

      <!-- Recomendações -->
      <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex items-center gap-2 mb-3">
          <LightBulbIcon class="w-5 h-5 text-yellow-500" />
          <h3 class="font-semibold text-gray-800">Recomendações de Orçamento</h3>
        </div>
        <div v-if="insights.recommendations.length" class="space-y-3">
          <div
            v-for="(rec, index) in insights.recommendations"
            :key="index"
            class="rounded-lg bg-green-50 border border-green-200 p-3"
          >
            <div class="flex items-center justify-between mb-1">
              <span class="text-sm font-medium text-green-800">{{ rec.action }}</span>
              <span class="text-sm font-bold text-green-700">
                Economize {{ formatCurrency(rec.potential_savings) }}
              </span>
            </div>
            <p class="text-xs text-green-600">{{ rec.reasoning }}</p>
          </div>
        </div>
        <p v-else class="text-sm text-gray-500">Sem recomendações no momento.</p>
      </div>
    </template>
  </div>
</template>
