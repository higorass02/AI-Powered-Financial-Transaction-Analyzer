<script setup lang="ts">
import { computed } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler,
} from 'chart.js'
import { formatMonth } from '@/utils/dates'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend,
  Filler
)

const props = defineProps<{
  data: Array<{
    month: string
    income: number
    spending: number
    net: number
  }>
}>()

const chartData = computed(() => ({
  labels: props.data.map(d => formatMonth(d.month)),
  datasets: [
    {
      label: 'Receitas',
      data: props.data.map(d => d.income),
      borderColor: '#10b981',
      backgroundColor: 'rgba(16, 185, 129, 0.1)',
      fill: false,
      tension: 0.4,
    },
    {
      label: 'Gastos',
      data: props.data.map(d => d.spending),
      borderColor: '#ef4444',
      backgroundColor: 'rgba(239, 68, 68, 0.1)',
      fill: false,
      tension: 0.4,
    },
  ],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'top' as const },
  },
  scales: {
    y: { beginAtZero: true },
  },
}
</script>

<template>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Tendência Mensal</h3>
    <div v-if="data.length" class="h-64">
      <Line :data="chartData" :options="chartOptions" />
    </div>
    <p v-else class="text-center text-gray-400 text-sm py-12">Sem dados para exibir</p>
  </div>
</template>
