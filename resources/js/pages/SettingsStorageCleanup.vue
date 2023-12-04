<template>
  <PageContainer>
    <div
      class="flex flex-col gap-4 bg-white border shadow-sm rounded-xl p-4 md:p-5 dark:bg-slate-900 dark:border-gray-700 dark:shadow-slate-700/[.7] xl:w-2/3 self-center"
    >
      <h3 class="text-xl font-medium text-gray-800 dark:text-white">
        Storage Cleanup
      </h3>

      <p class="text-gray-500 dark:text-gray-400">
        Clean up all messages and related files for all channels.
      </p>

      <button
        type="button"
        class="self-start py-3 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-red-500 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
        @click="onRunCleanup"
      >
        <RocketLaunchIcon class="w-5 h-5" />
        Run Cleanup
      </button>
    </div>
  </PageContainer>
</template>

<script lang="ts" setup>
import { RocketLaunchIcon } from '@heroicons/vue/24/outline'
import PageContainer from '../components/common/PageContainer.vue'
import { http } from '../http'
import { useAlert } from '../composables/use-alert'
import { useChannelsStore } from '../stores/channels'

const channelsStore = useChannelsStore()

async function onRunCleanup() {
  await http.post('settings/storage-cleanup')

  channelsStore.messages = []
  channelsStore.channels = channelsStore.channels.map((channel) => ({
    ...channel,
    unread_messages_count: 0,
  }))

  useAlert({
    title: 'Storage Cleanup Successful',
    message: 'All messages and related files have been deleted.',
    confirmButton: 'Continue',
    confirmationOnly: true,
  })
}
</script>
