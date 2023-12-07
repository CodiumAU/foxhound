<template>
  <div
    id="channel-sidebar"
    class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform hidden fixed top-0 start-0 xl:start-64 bottom-0 z-[60] w-96 bg-white border-e border-gray-200 pt-7 pb-10 overflow-y-auto xl:block xl:translate-x-0 xl:end-auto xl:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500 dark:bg-gray-800 dark:border-gray-700"
  >
    <nav class="p-3 pt-0 w-full flex flex-col">
      <div class="flex flex-row gap-1.5">
        <div class="grow">
          <label for="icon" class="sr-only">Search</label>
          <div class="relative">
            <div
              class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-4"
            >
              <MagnifyingGlassIcon
                class="flex-shrink-0 h-5 w-5 text-gray-400"
              />
            </div>
            <input
              id="icon"
              v-model="search"
              type="text"
              name="icon"
              class="py-2 px-4 ps-11 block w-full border-gray-200 rounded-lg text-sm text-gray-600 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
              placeholder="Search"
              :disabled="messages.length === 0 && !search"
            />
          </div>
        </div>

        <DropdownMenu id="settings">
          <template #button="{ id }">
            <IconButton v-bind="{ id }">
              <Cog6ToothIcon class="w-5 h-5" />
            </IconButton>
          </template>

          <DropdownMenuItem
            tag="button"
            danger
            :disabled="messages.length === 0 && !search"
            @click="clearMessages"
          >
            Clear Messages
          </DropdownMenuItem>
        </DropdownMenu>
      </div>

      <div
        v-if="messages.length === 0"
        class="text-center px-3 mt-6 font-light text-gray-500 dark:text-slate-400"
      >
        <template v-if="search">
          No messages found.<br />
          Try changing your search query.
        </template>
        <template v-else> There are no messages for this channel.</template>
      </div>

      <template v-else>
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
import {
  useChannelsStore,
  type ChannelListResource,
} from '../../stores/channels'
import { Cog6ToothIcon } from '@heroicons/vue/24/outline'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import { useAlert } from '../../composables/use-alert'
import { useRouter } from 'vue-router'
import { computed, watch } from 'vue'
import ChannelSidebarItem from './ChannelSidebarItem.vue'
import DropdownMenu from '../common/DropdownMenu.vue'
import DropdownMenuItem from '../common/DropdownMenuItem.vue'
import IconButton from '../common/IconButton.vue'
import Fuse from 'fuse.js'

const props = defineProps<{
  channel: ChannelListResource
}>()

const router = useRouter()
const channelsStore = useChannelsStore()
const { search } = storeToRefs(channelsStore)

const messages = computed(() => {
  if (!search.value) {
    return channelsStore.messages
  }

  let query = search.value
  let keys = [
    'subject',
    'recipients.address',
    'data.from.address',
    'data.cc.address',
    'data.bcc.address',
    'data.replyTo.address',
  ]

  const prefixes = {
    to: 'recipients.address',
    from: 'data.from.address',
    cc: 'data.cc.address',
    bcc: 'data.bcc.address',
    'reply to': 'data.replyTo.address',
  }

  for (const [prefix, key] of Object.entries(prefixes)) {
    if (query.startsWith(`${prefix}:`)) {
      keys = [key]
      query = query.replace(`${prefix}:`, '').trim()

      break
    }
  }

  const fuse = new Fuse(channelsStore.messages, {
    keys,
    threshold: 0.2,
    ignoreLocation: true,
  })

  return fuse.search(query).map(({ item }) => item)
})

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

  search.value = null

  router.replace({
    name: 'channels.single',
    params: {
      channel: props.channel.key,
    },
  })
}

watch(
  () => props.channel.key,
  () => {
    search.value = null
  }
)
</script>
