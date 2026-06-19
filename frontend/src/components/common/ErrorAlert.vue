<script setup lang="ts">
import { XCircleIcon } from '@heroicons/vue/24/outline'

defineProps<{
  message: string
}>()

const emit = defineEmits<{ dismiss: [] }>()
</script>

<template>
  <div class="rounded-lg bg-red-50 border border-red-200 p-4 flex items-start gap-3">
    <XCircleIcon class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" />
    <p class="text-sm text-red-700 flex-1">{{ message }}</p>
    <button class="text-red-400 hover:text-red-600" @click="emit('dismiss')">
      <XCircleIcon class="w-4 h-4" />
    </button>
  </div>
</template>
