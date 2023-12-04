<template>
  <ChannelSidebar v-if="channel" v-bind="{ channel }" />

  <PageContainer>
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

watch(
  () => props.channel,
  async () => {
    await channelsStore.getMessages(props.channel)

    markMessageAsRead(props.uuid)
  },
  { immediate: true }
)

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

// Periodically fetch messages.
const timeout = ref<number>()

watch(
  channel,
  async () => {
    if (timeout.value) {
      clearTimeout(timeout.value)
    }

    timeout.value = setTimeout(async () => {
      await channelsStore.getMessages(props.channel)
    }, 5000)
  },
  { immediate: true }
)

onBeforeUnmount(() => {
  clearTimeout(timeout.value)
})
</script>
