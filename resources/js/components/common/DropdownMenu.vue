<template>
  <div class="hs-dropdown relative">
    <slot name="button" v-bind="{ id: `hs-dropdown-${id}` }" />

    <div
      class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-[15rem] bg-white shadow-md rounded-lg p-2 mt-2 dark:bg-gray-800 dark:border dark:border-gray-700 dark:divide-gray-700 after:h-4 after:absolute after:-bottom-4 after:start-0 after:w-full before:h-4 before:absolute before:-top-4 before:start-0 before:w-full"
      :aria-labelledby="`hs-dropdown-${id}`"
    >
      <slot />
    </div>
  </div>
</template>

<script lang="ts" setup>
defineProps<{
  id: string
}>()
</script>
