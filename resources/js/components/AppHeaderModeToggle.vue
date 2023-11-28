<template>
  <AppHeaderButton @click="toggleMode">
    <SunIcon v-if="mode === 'light'" class="h-6 w-6" />
    <MoonIcon v-else-if="mode === 'dark'" class="h-6 w-6" />
  </AppHeaderButton>
</template>

<script lang="ts" setup>
import { SunIcon, MoonIcon } from '@heroicons/vue/24/outline'
import { useLocalStorage } from '@vueuse/core'
import { onMounted, watch } from 'vue'
import AppHeaderButton from './AppHeaderButton.vue'

const system = window.matchMedia('(prefers-color-scheme: dark)').matches
  ? 'dark'
  : 'light'
const mode = useLocalStorage('mode', system)

watch(mode, applyMode)

function applyMode() {
  if (mode.value === 'light') {
    document.documentElement.classList.remove('dark')
  } else {
    document.documentElement.classList.add('dark')
  }
}

onMounted(applyMode)

function toggleMode() {
  mode.value = mode.value === 'light' ? 'dark' : 'light'
}
</script>
