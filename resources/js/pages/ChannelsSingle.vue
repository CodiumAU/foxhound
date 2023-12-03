<template>
  <ChannelSidebar v-bind="{ channel }" />

  <PageContainer>
    <div
      v-if="uuid === undefined || uuid === ''"
      class="flex items-center justify-center h-full text-2xl font-light text-gray-500 dark:text-slate-400"
    >
      Select a message to view.
    </div>
    <template v-else>
      <Component
        :is="headerComponent"
        v-if="message !== undefined"
        v-bind="{ message }"
      />

      <div
        class="rounded-lg h-full w-full border border-gray-200 dark:border-gray-700 bg-white overflow-hidden"
      >
        <iframe v-if="iframeSource" :src="iframeSource" class="w-full h-full" />
      </div>
    </template>
  </PageContainer>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { ChannelType, useChannelsStore } from '../stores/channels'
import { http } from '../http'
import { computed, watch } from 'vue'
import ChannelSidebar from '../components/ChannelSidebar.vue'
import PageContainer from '../components/PageContainer.vue'
import ChannelHeaderMail from '../components/ChannelHeaderMail.vue'
import ChannelHeaderSms from '../components/ChannelHeaderSms.vue'

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
const { messages } = storeToRefs(channelsStore)

watch(
  () => props.channel,
  async () => {
    await channelsStore.getMessages(props.channel)

    markMessageAsRead(props.uuid)
  },
  { immediate: true }
)

watch(() => props.uuid, markMessageAsRead)

const message = computed(() =>
  messages.value.find((message) => message.uuid === props.uuid)
)
const iframeSource = computed(() => {
  if (props.uuid === undefined || props.uuid === '') {
    return null
  }

  return http.getUri({ url: `/channels/${props.channel}/${props.uuid}/html` })
})

const headerComponent = computed(() => {
  switch (props.channel) {
    case ChannelType.Mail:
      return ChannelHeaderMail
    case ChannelType.Sms:
      return ChannelHeaderSms
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
