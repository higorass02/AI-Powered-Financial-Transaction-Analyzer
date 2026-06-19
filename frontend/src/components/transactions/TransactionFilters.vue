<script setup lang="ts">
import { ref } from 'vue'
import type { TransactionFilters } from '@/types'
import { useTransactionStore } from '@/stores/transactions'

const emit = defineEmits<{ filter: [filters: TransactionFilters] }>()
const store = useTransactionStore()

const filters = ref<TransactionFilters>({
  type: undefined,
  category_id: undefined,
  from_date: '',
  to_date: '',
  search: '',
})

function apply() {
  emit('filter', filters.value)
}

function reset() {
  filters.value = { type: undefined, category_id: undefined, from_date: '', to_date: '', search: '' }
  emit('filter', {})
}
</script>

<template>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
      <input
        v-model="filters.search"
        type="text"
        placeholder="Buscar..."
        class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500 col-span-2 lg:col-span-1"
        @keyup.enter="apply"
      />
      <select
        v-model="filters.type"
        class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
      >
        <option :value="undefined">Todos os tipos</option>
        <option value="debit">Débito</option>
        <option value="credit">Crédito</option>
        <option value="pix">Pix</option>
      </select>
      <select
        v-model="filters.category_id"
        class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
      >
        <option :value="undefined">Todas as categorias</option>
        <option v-for="cat in store.categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
      </select>
      <input
        v-model="filters.from_date"
        type="date"
        class="rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
      />
      <div class="flex gap-2">
        <input
          v-model="filters.to_date"
          type="date"
          class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500"
        />
        <button
          class="px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
          @click="apply"
        >
          Filtrar
        </button>
        <button
          class="px-3 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50"
          @click="reset"
        >
          Limpar
        </button>
      </div>
    </div>
  </div>
</template>
