<script setup lang="ts">
import { formatCurrency } from '@/utils/currency'
import { formatDate } from '@/utils/dates'
import { transactionTypeLabel } from '@/utils/formatters'
import type { Transaction } from '@/types'
import {
  TrashIcon,
  MagnifyingGlassIcon,
  SparklesIcon,
} from '@heroicons/vue/24/outline'

defineProps<{
  transactions: Transaction[]
  loading?: boolean
}>()

const emit = defineEmits<{
  delete: [id: number]
  analyze: [transaction: Transaction]
}>()

const typeColors: Record<string, string> = {
  debit: 'bg-red-100 text-red-700',
  credit: 'bg-green-100 text-green-700',
  pix: 'bg-blue-100 text-blue-700',
}
</script>

<template>
  <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="text-left px-4 py-3 text-gray-500 font-medium">Descrição</th>
            <th class="text-left px-4 py-3 text-gray-500 font-medium">Tipo</th>
            <th class="text-left px-4 py-3 text-gray-500 font-medium">Categoria</th>
            <th class="text-right px-4 py-3 text-gray-500 font-medium">Valor</th>
            <th class="text-left px-4 py-3 text-gray-500 font-medium">Data</th>
            <th class="text-center px-4 py-3 text-gray-500 font-medium">IA</th>
            <th class="px-4 py-3" />
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="text-center py-12 text-gray-400">Carregando...</td>
          </tr>
          <tr v-else-if="!transactions.length">
            <td colspan="7" class="text-center py-12 text-gray-400">
              Nenhuma transação encontrada
            </td>
          </tr>
          <tr
            v-for="tx in transactions"
            v-else
            :key="tx.id"
            class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
          >
            <td class="px-4 py-3">
              <p class="font-medium text-gray-800 truncate max-w-xs">{{ tx.description }}</p>
            </td>
            <td class="px-4 py-3">
              <span :class="['px-2 py-1 rounded-full text-xs font-medium', typeColors[tx.type]]">
                {{ transactionTypeLabel(tx.type) }}
              </span>
            </td>
            <td class="px-4 py-3 text-gray-500">
              {{ tx.category?.name ?? '—' }}
            </td>
            <td
              :class="[
                'px-4 py-3 text-right font-semibold',
                tx.type === 'credit' ? 'text-green-600' : 'text-red-600',
              ]"
            >
              {{ tx.type === 'credit' ? '+' : '-' }}{{ formatCurrency(tx.amount) }}
            </td>
            <td class="px-4 py-3 text-gray-500">{{ formatDate(tx.date) }}</td>
            <td class="px-4 py-3 text-center">
              <span v-if="tx.ai_analysis" :class="[
                'text-xs px-2 py-0.5 rounded-full',
                tx.ai_analysis.confidence >= 0.7 ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700',
              ]">
                {{ Math.round(tx.ai_analysis.confidence * 100) }}%
              </span>
              <button
                v-else
                class="p-1 text-gray-400 hover:text-blue-500"
                title="Analisar com IA"
                @click="emit('analyze', tx)"
              >
                <SparklesIcon class="w-4 h-4" />
              </button>
            </td>
            <td class="px-4 py-3">
              <button
                class="p-1 text-gray-400 hover:text-red-500"
                @click="emit('delete', tx.id)"
              >
                <TrashIcon class="w-4 h-4" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
