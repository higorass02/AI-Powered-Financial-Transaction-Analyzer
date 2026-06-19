<script setup lang="ts">
import AppHeader from '@/components/common/AppHeader.vue'
import AppSidebar from '@/components/common/AppSidebar.vue'
import ToastContainer from '@/components/common/ToastContainer.vue'
import TransactionForm from '@/components/transactions/TransactionForm.vue'
import { useUiStore } from '@/stores/ui'

const uiStore = useUiStore()
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-gray-50">
    <AppSidebar />

    <div class="flex-1 flex flex-col overflow-hidden min-w-0">
      <AppHeader />

      <main class="flex-1 overflow-y-auto p-4 lg:p-6">
        <router-view />
      </main>
    </div>

    <!-- New transaction modal -->
    <div
      v-if="uiStore.showTransactionModal"
      class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
      @click.self="uiStore.closeTransactionModal"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6">
        <h2 class="font-semibold text-gray-900 mb-4">Nova Transação</h2>
        <TransactionForm @close="uiStore.closeTransactionModal" @saved="uiStore.closeTransactionModal" />
      </div>
    </div>

    <ToastContainer />
  </div>
</template>
