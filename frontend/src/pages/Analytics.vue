<script setup lang="ts">
import { onMounted } from 'vue'
import SpendingChart from '@/components/analytics/SpendingChart.vue'
import TrendChart from '@/components/analytics/TrendChart.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'
import { useAnalyticsStore } from '@/stores/analytics'
import { formatCurrency } from '@/utils/currency'

const store = useAnalyticsStore()

onMounted(() => {
  store.fetchSpendingByCategory()
  store.fetchMonthlyTrend(12)
})
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-xl font-bold text-gray-900">Analytics</h1>

    <LoadingSpinner v-if="store.loading" text="Carregando dados..." />

    <template v-else>
      <div class="grid lg:grid-cols-2 gap-4">
        <SpendingChart :data="(store.spendingByCategory as any[])" />
        <TrendChart :data="(store.monthlyTrend as any[])" />
      </div>

      <!-- Category breakdown table -->
      <div class="bg-white rounded-xl border border-gray-200">
        <div class="p-4 border-b border-gray-200">
          <h2 class="font-semibold text-gray-800">Detalhamento por Categoria</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="text-left px-4 py-3 text-gray-500">Categoria</th>
                <th class="text-right px-4 py-3 text-gray-500">Total</th>
                <th class="text-right px-4 py-3 text-gray-500">%</th>
                <th class="text-right px-4 py-3 text-gray-500">Transações</th>
                <th class="text-right px-4 py-3 text-gray-500">Média</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in (store.spendingByCategory as any[])"
                :key="(item as any).category_id"
                class="border-t border-gray-100 hover:bg-gray-50"
              >
                <td class="px-4 py-3 font-medium text-gray-800">{{ (item as any).category_name }}</td>
                <td class="px-4 py-3 text-right text-red-600 font-semibold">
                  {{ formatCurrency((item as any).amount) }}
                </td>
                <td class="px-4 py-3 text-right text-gray-500">{{ (item as any).percentage }}%</td>
                <td class="px-4 py-3 text-right text-gray-500">{{ (item as any).transactions_count }}</td>
                <td class="px-4 py-3 text-right text-gray-500">
                  {{ formatCurrency((item as any).average_transaction) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>
  </div>
</template>
