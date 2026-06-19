<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useTransactionStore } from '@/stores/transactions'
import type { CreateTransactionData } from '@/types'
import { today } from '@/utils/dates'

const emit = defineEmits<{ close: []; saved: [] }>()

const store = useTransactionStore()

const form = ref<CreateTransactionData>({
  description: '',
  amount: 0,
  type: 'debit',
  date: today(),
  category_id: null,
})

const submitting = ref(false)
const errors = ref<Record<string, string>>({})

onMounted(() => store.fetchCategories())

async function submit() {
  errors.value = {}
  submitting.value = true
  try {
    await store.create(form.value)
    emit('saved')
    emit('close')
  } catch (e: unknown) {
    if (e instanceof Error) errors.value.general = e.message
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <form class="space-y-4" @submit.prevent="submit">
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
      <input
        v-model="form.description"
        type="text"
        class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
        placeholder="Ex: UBER BRASIL"
        required
        minlength="3"
        maxlength="255"
      />
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Valor (R$)</label>
        <input
          v-model.number="form.amount"
          type="number"
          step="0.01"
          min="0.01"
          max="100000"
          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
        <select
          v-model="form.type"
          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
        >
          <option value="debit">Débito</option>
          <option value="credit">Crédito</option>
          <option value="pix">Pix</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
        <input
          v-model="form.date"
          type="date"
          :max="today()"
          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
          required
        />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
        <select
          v-model="form.category_id"
          class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500"
        >
          <option :value="null">IA vai categorizar</option>
          <option v-for="cat in store.categories" :key="cat.id" :value="cat.id">
            {{ cat.name }}
          </option>
        </select>
      </div>
    </div>

    <p v-if="errors.general" class="text-sm text-red-600">{{ errors.general }}</p>

    <div class="flex gap-2 pt-2">
      <button
        type="button"
        class="flex-1 py-2 px-4 rounded-lg border border-gray-300 text-sm text-gray-700 hover:bg-gray-50"
        @click="emit('close')"
      >
        Cancelar
      </button>
      <button
        type="submit"
        :disabled="submitting"
        class="flex-1 py-2 px-4 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 disabled:opacity-50"
      >
        {{ submitting ? 'Salvando...' : 'Salvar' }}
      </button>
    </div>
  </form>
</template>
