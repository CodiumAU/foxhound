<template>
  <div
    id="application-sidebar"
    class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform hidden fixed top-0 start-0 bottom-0 z-[60] w-64 bg-gray-900 border-e border-gray-800 pt-4 pb-10 overflow-y-auto xl:block xl:translate-x-0 xl:end-auto xl:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500"
  >
    <div class="px-6">
      <a
        class="flex-none text-2xl font-semibold text-slate-300 focus:outline-none focus:ring-1 focus:ring-gray-600 font-serif"
        aria-label="Brand"
      >
        foxhound
      </a>
    </div>

    <nav class="p-6 w-full flex flex-col flex-wrap">
      <ul class="space-y-1.5">
        <li class="text-base text-slate-300 pb-2 font-semibold">Channels</li>

        <AppSidebarChannelItem
          v-for="channel in channels"
          :key="channel.key"
          v-bind="{ channel }"
        />

        <li class="text-base text-slate-300 pb-2 font-semibold">Settings</li>

        <AppSidebarItem
          :to="{ name: 'settings.storage-cleanup' }"
          :icon="TrashIcon"
        >
          Storage Cleanup
        </AppSidebarItem>
      </ul>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import { useChannelsStore } from '../../stores/channels'
import { storeToRefs } from 'pinia'
import { computed } from 'vue'
import { useFavicon } from '@vueuse/core'
import { TrashIcon } from '@heroicons/vue/24/outline'
import AppSidebarChannelItem from './AppSidebarChannelItem.vue'
import AppSidebarItem from './AppSidebarItem.vue'

const channelsStore = useChannelsStore()
const { channels } = storeToRefs(channelsStore)

await channelsStore.getChannels()

const favicon = computed(() =>
  channels.value.some((channel) => channel.unread_messages_count > 0)
    ? '/vendor/foxhound/img/unread.png'
    : '/vendor/foxhound/img/read.png'
)
useFavicon(favicon)
</script>
