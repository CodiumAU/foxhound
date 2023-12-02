<template>
  <Component
    :is="tag"
    class="flex items-center w-full gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-300"
    :class="{
      'text-red-500 hover:bg-red-500 hover:text-white dark:text-red-500 dark:hover:text-white dark:hover:bg-red-500':
        danger,
    }"
  >
    <slot />
  </Component>
</template>

<script setup lang="ts">
withDefaults(
  defineProps<{
    tag?: string
    danger?: boolean
  }>(),
  {
    tag: 'a',
    danger: false,
  }
)
</script>
