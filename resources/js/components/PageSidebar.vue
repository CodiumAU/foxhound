<template>
  <div
    class="shrink-0 w-96 bg-white border-e border-gray-200 pt-7 pb-10 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500 dark:bg-gray-800 dark:border-gray-700"
  >
    <nav class="p-3 pt-0 w-full flex flex-col">
      <ul class="space-y-1.5">
        <PageSidebarItem
          v-for="message in messages"
          :key="message.uuid"
          v-bind="{ message, route }"
        />
      </ul>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import type { RouteLocationNamedRaw } from 'vue-router'
import type { MessageListResource } from '../stores/channels-mail'
import PageSidebarItem from './PageSidebarItem.vue'

defineProps<{
  messages: MessageListResource[]
  route: RouteLocationNamedRaw
}>()
</script>
