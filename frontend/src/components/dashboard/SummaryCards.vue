<script setup lang="ts">
import { computed } from 'vue'
import { formatCurrency } from '@/utils/currency'
import {
  BanknotesIcon,
  ArrowUpCircleIcon,
  ArrowDownCircleIcon,
  ChartPieIcon,
} from '@heroicons/vue/24/outline'

const props = defineProps<{
  summary: {
    balance: { total: number; currency: string }
    current_month: { income: number; spending: number; net: number }
    budget: { total: number; spent: number; percentage: number }
  } | null
}>()

const cards = computed(() => {
  if (!props.summary) return []
  return [
    {
      label: 'Saldo Total',
      value: formatCurrency(props.summary.balance.total),
      icon: BanknotesIcon,
      color: 'text-blue-600',
      bg: 'bg-blue-50',
    },
    {
      label: 'Receitas do Mês',
      value: formatCurrency(props.summary.current_month.income),
      icon: ArrowUpCircleIcon,
      color: 'text-green-600',
      bg: 'bg-green-50',
    },
    {
      label: 'Gastos do Mês',
      value: formatCurrency(props.summary.current_month.spending),
      icon: ArrowDownCircleIcon,
      color: 'text-red-600',
      bg: 'bg-red-50',
    },
    {
      label: 'Orçamento Usado',
      value: `${props.summary.budget.percentage}%`,
      icon: ChartPieIcon,
      color: 'text-purple-600',
      bg: 'bg-purple-50',
    },
  ]
})
</script>

<template>
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div
      v-for="card in cards"
      :key="card.label"
      class="bg-white rounded-xl border border-gray-200 p-4"
    >
      <div class="flex items-center justify-between mb-3">
        <p class="text-sm text-gray-500">{{ card.label }}</p>
        <div :class="['p-2 rounded-lg', card.bg]">
          <component :is="card.icon" :class="['w-5 h-5', card.color]" />
        </div>
      </div>
      <p class="text-xl font-bold text-gray-900">{{ card.value }}</p>
    </div>
  </div>
</template>
