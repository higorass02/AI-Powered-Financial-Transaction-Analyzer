import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useUiStore = defineStore('ui', () => {
  const sidebarOpen = ref(false)
  const showTransactionModal = ref(false)
  const showImportModal = ref(false)
  const toasts = ref<Array<{ id: string; type: 'success' | 'error' | 'warning'; message: string }>>([])

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  function openTransactionModal() {
    showTransactionModal.value = true
  }

  function closeTransactionModal() {
    showTransactionModal.value = false
  }

  function addToast(type: 'success' | 'error' | 'warning', message: string) {
    const id = Date.now().toString()
    toasts.value.push({ id, type, message })
    setTimeout(() => removeToast(id), 5000)
  }

  function removeToast(id: string) {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  return {
    sidebarOpen,
    showTransactionModal,
    showImportModal,
    toasts,
    toggleSidebar,
    openTransactionModal,
    closeTransactionModal,
    addToast,
    removeToast,
  }
})
