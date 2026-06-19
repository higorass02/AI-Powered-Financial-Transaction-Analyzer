<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useUiStore } from '@/stores/ui'
import {
  HomeIcon,
  CreditCardIcon,
  ChartBarIcon,
  SparklesIcon,
  Cog6ToothIcon,
} from '@heroicons/vue/24/outline'

const route = useRoute()
const uiStore = useUiStore()

const navItems = [
  { label: 'Dashboard', icon: HomeIcon, to: '/dashboard' },
  { label: 'Transações', icon: CreditCardIcon, to: '/transactions' },
  { label: 'Analytics', icon: ChartBarIcon, to: '/analytics' },
  { label: 'IA & Insights', icon: SparklesIcon, to: '/ai' },
  { label: 'Configurações', icon: Cog6ToothIcon, to: '/settings' },
]

const isActive = (path: string) => route.path === path
</script>

<template>
  <aside
    :class="[
      'fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 text-white transform transition-transform duration-200',
      'lg:relative lg:translate-x-0',
      uiStore.sidebarOpen ? 'translate-x-0' : '-translate-x-full',
    ]"
  >
    <div class="p-4 border-b border-gray-700">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-sm">FA</span>
        </div>
        <span class="font-bold text-white">Financial AI</span>
      </div>
    </div>

    <nav class="p-3">
      <router-link
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :class="[
          'flex items-center gap-3 px-3 py-2.5 rounded-lg mb-1 transition-colors text-sm font-medium',
          isActive(item.to)
            ? 'bg-blue-600 text-white'
            : 'text-gray-400 hover:bg-gray-800 hover:text-white',
        ]"
        @click="uiStore.sidebarOpen = false"
      >
        <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
        {{ item.label }}
      </router-link>
    </nav>
  </aside>

  <!-- Mobile overlay -->
  <div
    v-if="uiStore.sidebarOpen"
    class="fixed inset-0 bg-black/50 z-40 lg:hidden"
    @click="uiStore.sidebarOpen = false"
  />
</template>
