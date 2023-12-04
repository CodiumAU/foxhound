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
import { computed, watch } from 'vue'
import ChannelSidebar from '../components/ChannelSidebar.vue'
import PageContainer from '../components/PageContainer.vue'
import ChannelHeaderMail from '../components/mail/ChannelHeader.vue'
import ChannelBodyMail from '../components/mail/ChannelBody.vue'
import ChannelHeaderSms from '../components/sms/ChannelHeader.vue'
import ChannelBodySms from '../components/sms/ChannelBody.vue'

const props = withDefaults(
  defineProps<{
    channel: ChannelType
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
}
</script>
