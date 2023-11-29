<template>
  <div
    class="shrink-0 w-96 bg-white border-e border-gray-200 pt-7 pb-10 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500 dark:bg-gray-800 dark:border-gray-700"
  >
    <nav class="p-3 pt-0 w-full flex flex-col">
      <div
        v-if="messages.length === 0"
        class="text-center px-3 font-light text-gray-500 dark:text-slate-400"
      >
        There are no messages for this channel.
      </div>
      <ul class="space-y-1.5">
        <ChannelSidebarItem
          v-for="message in messages"
          :key="message.uuid"
          v-bind="{ message, channel }"
        />
      </ul>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { useChannelsStore } from '../stores/channels'
import ChannelSidebarItem from './ChannelSidebarItem.vue'

defineProps<{
  channel: string
}>()

const channelsStore = useChannelsStore()
const { messages } = storeToRefs(channelsStore)
</script>
