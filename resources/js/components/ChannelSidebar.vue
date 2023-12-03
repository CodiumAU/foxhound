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

      <template v-else>
        <div class="flex flex-row justify-end">
          <DropdownMenu id="settings">
            <template #button="{ id }">
              <IconButton v-bind="{ id }">
                <Cog6ToothIcon class="w-5 h-5" />
              </IconButton>
            </template>

            <DropdownMenuItem tag="button" danger @click="clearMessages">
              Clear Messages
            </DropdownMenuItem>
          </DropdownMenu>
        </div>
        <ul class="space-y-1.5 mt-3">
          <ChannelSidebarItem
            v-for="message in messages"
            :key="message.uuid"
            v-bind="{ message, channel }"
          />
        </ul>
      </template>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { useChannelsStore, type ChannelListResource } from '../stores/channels'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'
import { useAlert } from '../composables/use-alert'
import { useRouter } from 'vue-router'
import ChannelSidebarItem from './ChannelSidebarItem.vue'
import DropdownMenu from './DropdownMenu.vue'
import DropdownMenuItem from './DropdownMenuItem.vue'
import IconButton from './IconButton.vue'

const props = defineProps<{
  channel: ChannelListResource
}>()

const router = useRouter()
const channelsStore = useChannelsStore()
const { messages } = storeToRefs(channelsStore)

async function clearMessages() {
  if (
    !(await useAlert({
      danger: true,
      title: 'Clear Messages',
      message: 'Are you sure you want to clear all messages?',
      confirmButton: 'Clear',
    }))
  ) {
    return
  }

  await channelsStore.clearMessages(props.channel.key)

  router.replace({
    name: 'channels.single',
    params: {
      channel: props.channel.key,
    },
  })
}
</script>
