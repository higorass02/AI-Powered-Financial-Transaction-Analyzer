<script setup lang="ts">
import { useUiStore } from '@/stores/ui'
import { CheckCircleIcon, ExclamationCircleIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const uiStore = useUiStore()

const icons = {
  success: CheckCircleIcon,
  error: ExclamationCircleIcon,
  warning: ExclamationCircleIcon,
}

const colors = {
  success: 'bg-green-50 border-green-200 text-green-800',
  error: 'bg-red-50 border-red-200 text-red-800',
  warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
}
</script>

<template>
  <div class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 max-w-sm w-full">
    <div
      v-for="toast in uiStore.toasts"
      :key="toast.id"
      :class="['flex items-start gap-3 border rounded-lg px-4 py-3 shadow-lg', colors[toast.type]]"
    >
      <component :is="icons[toast.type]" class="w-5 h-5 flex-shrink-0 mt-0.5" />
      <p class="text-sm flex-1">{{ toast.message }}</p>
      <button @click="uiStore.removeToast(toast.id)">
        <XMarkIcon class="w-4 h-4" />
      </button>
    </div>
  </div>
</template>
