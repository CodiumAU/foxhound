<template>
  <ChannelSidebar v-if="channel" v-bind="{ channel }" />

  <PageContainer class="xl:ps-[24rem]">
    <button
      type="button"
      class="grow py-3 px-4 mb-4 flex xl:hidden items-center gap-x-2 text-normal font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
      data-hs-overlay="#channel-sidebar"
    >
      <QueueListIcon class="h-6 w-6 flex-shrink-0" />
      Messages
    </button>

    <div
      v-if="uuid === undefined || uuid === ''"
      class="grow flex items-center justify-center h-full text-2xl font-light text-gray-500 dark:text-slate-400"
    >
      Select a message to view.
    </div>
    <template v-else>
      <Component
        :is="headerComponent"
        v-if="message !== undefined"
        v-bind="{ message }"
      />

      <Component :is="bodyComponent" v-bind="{ uuid, channel }" />
    </template>
  </PageContainer>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { ChannelType, useChannelsStore } from '../stores/channels'
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { QueueListIcon } from '@heroicons/vue/24/outline'
import ChannelSidebar from '../components/channels/ChannelSidebar.vue'
import PageContainer from '../components/common/PageContainer.vue'
import ChannelHeaderMail from '../components/channels/mail/ChannelHeader.vue'
import ChannelBodyMail from '../components/channels/mail/ChannelBody.vue'
import ChannelHeaderSms from '../components/channels/sms/ChannelHeader.vue'
import ChannelBodySms from '../components/channels/sms/ChannelBody.vue'

const props = withDefaults(
  defineProps<{
    channel: string
    uuid?: string
  }>(),
  {
    uuid: undefined,
  }
)

const channelsStore = useChannelsStore()
const { messages, channels } = storeToRefs(channelsStore)
const interval = ref<number>()

watch(
  () => props.channel,
  async () => {
    await channelsStore.getMessages(props.channel)

    markMessageAsRead(props.uuid)

    if (interval.value) {
      clearInterval(interval.value)
    }

    interval.value = setInterval(async () => {
      await channelsStore.getMessages(props.channel)
    }, 5000)
  },
  { immediate: true }
)

onBeforeUnmount(() => {
  clearInterval(interval.value)
})

watch(() => props.uuid, markMessageAsRead)

const channel = computed(() =>
  channels.value.find((channel) => channel.key === props.channel)
)

const message = computed(() =>
  messages.value.find((message) => message.uuid === props.uuid)
)

const headerComponent = computed(() => {
  if (!channel.value) {
    return null
  }

  switch (channel.value.type) {
    case ChannelType.Mail:
      return ChannelHeaderMail
    case ChannelType.Sms:
      return ChannelHeaderSms
    default:
      return null
  }
})

const bodyComponent = computed(() => {
  if (!channel.value) {
    return null
  }

  switch (channel.value.type) {
    case ChannelType.Mail:
      return ChannelBodyMail
    case ChannelType.Sms:
      return ChannelBodySms
    default:
      return null
  }
})

function markMessageAsRead(uuid?: string) {
  const message = messages.value.find((message) => message.uuid === uuid)

  if (message === undefined) {
    return
  }

  messages.value.splice(messages.value.indexOf(message), 1, {
    ...message,
    unread: false,
  })

  // Set the count of unread messages on the channel state.
  updateUnreadMessagesCount()
}

function updateUnreadMessagesCount() {
  if (channel.value === undefined) {
    return
  }

  channels.value.splice(
    channels.value.findIndex(({ key }) => key === props.channel),
    1,
    {
      ...channel.value,
      unread_messages_count: messages.value.reduce(
        (count, { unread }) => count + (unread ? 1 : 0),
        0
      ),
    }
  )
}
</script>
