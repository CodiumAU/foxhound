<template>
  <span
    class="shrink-0 inline-flex items-center leading-none gap-x-1.5 py-2 px-2 rounded-full text-sm font-normal border border-gray-200 bg-white text-gray-800 dark:bg-slate-900 dark:border-gray-700 dark:text-white"
  >
    <template v-if="name">
      {{ name }}
    </template>
    <span class="text-gray-500 dark:text-slate-400 font-normal">
      {{ address }}
    </span>
  </span>
</template>

<script lang="ts" setup>
defineProps<{
  name: string | null
  address: string
}>()
</script>
