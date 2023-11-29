<template>
  <ChannelSidebar v-bind="{ channel }" />

  <PageContainer>
    <template v-if="uuid === undefined || uuid === ''">
      Select a message.
    </template>
    <template v-else-if="iframeSource">
      <div
        class="rounded-lg h-full w-full border border-gray-200 dark:border-gray-700 bg-white overflow-hidden"
      >
        <iframe :src="iframeSource" class="w-full h-full" />
      </div>
    </template>
  </PageContainer>
</template>

<script lang="ts" setup>
import { storeToRefs } from 'pinia'
import { useChannelsStore } from '../stores/channels'
import { http } from '../http'
import { computed, watch } from 'vue'
import ChannelSidebar from '../components/ChannelSidebar.vue'
import PageContainer from '../components/PageContainer.vue'

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
const { messages } = storeToRefs(channelsStore)

watch(
  () => props.channel,
  async () => {
    await channelsStore.getMessages(props.channel)
  },
  { immediate: true }
)

const iframeSource = computed(() => {
  if (props.uuid === undefined || props.uuid === '') {
    return null
  }

  return http.getUri({ url: `/channels/${props.channel}/${props.uuid}/html` })
})

watch(
  () => props.uuid,
  (uuid) => {
    const message = messages.value.find((message) => message.uuid === uuid)

    if (message === undefined) {
      return
    }

    messages.value.splice(messages.value.indexOf(message), 1, {
      ...message,
      unread: false,
    })
  },
  { immediate: true }
)
</script>