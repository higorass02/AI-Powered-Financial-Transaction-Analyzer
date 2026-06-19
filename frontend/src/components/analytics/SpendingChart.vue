<script setup lang="ts">
import { computed } from 'vue'
import { Doughnut } from 'vue-chartjs'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
} from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend)

const props = defineProps<{
  data: Array<{
    category_name: string
    amount: number
    percentage: number
  }>
}>()

const chartData = computed(() => ({
  labels: props.data.map(d => `${d.category_name} (${d.percentage}%)`),
  datasets: [
    {
      data: props.data.map(d => d.amount),
      backgroundColor: [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4',
        '#FFEAA7', '#DDA0DD', '#98D8C8', '#B0C4DE',
        '#90EE90', '#FFD700', '#D3D3D3',
      ],
      borderWidth: 2,
      borderColor: '#fff',
    },
  ],
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'right' as const },
  },
}
</script>

<template>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Gastos por Categoria</h3>
    <div v-if="data.length" class="h-64">
      <Doughnut :data="chartData" :options="chartOptions" />
    </div>
    <p v-else class="text-center text-gray-400 text-sm py-12">Sem dados para exibir</p>
  </div>
</template>
