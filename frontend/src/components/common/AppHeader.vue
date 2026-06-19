<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useUiStore } from '@/stores/ui'
import { useAuth } from '@/composables/useAuth'
import { Bars3Icon, BellIcon, UserCircleIcon } from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const uiStore = useUiStore()
const { logout } = useAuth()
</script>

<template>
  <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <button
        class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 lg:hidden"
        @click="uiStore.toggleSidebar"
      >
        <Bars3Icon class="w-5 h-5" />
      </button>
      <span class="font-semibold text-gray-800 text-lg">Financial Analyzer</span>
    </div>

    <div class="flex items-center gap-2">
      <button class="p-2 rounded-lg text-gray-500 hover:bg-gray-100">
        <BellIcon class="w-5 h-5" />
      </button>
      <div class="flex items-center gap-2 pl-2 border-l border-gray-200">
        <UserCircleIcon class="w-8 h-8 text-gray-400" />
        <div class="hidden sm:block">
          <p class="text-sm font-medium text-gray-800">{{ authStore.user?.name }}</p>
          <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
        </div>
        <button
          class="text-xs text-gray-500 hover:text-red-500 ml-2"
          @click="logout"
        >
          Sair
        </button>
      </div>
    </div>
  </header>
</template>
