<template>
  <RouterLink v-slot="{ isActive, href, navigate }" custom v-bind="{ to }">
    <li>
      <a
        class="flex items-center gap-x-3 py-2 px-2.5 text-sm rounded-lg focus:outline-none focus:ring-1 focus:ring-gray-600"
        :class="{
          'bg-gray-700 text-white': isActive,
          'text-slate-400 hover:bg-gray-700 hover:text-white': !isActive,
        }"
        v-bind="{ href }"
        @click="navigate"
      >
        <Component :is="icon" class="flex-shrink-0 w-5 h-5" />
        <slot />
      </a>
    </li>
  </RouterLink>
</template>

<script lang="ts" setup>
import type { FunctionalComponent } from 'vue'
import type { RouteLocationRaw } from 'vue-router'

defineProps<{
  to: RouteLocationRaw
  icon: FunctionalComponent
}>()
</script>
