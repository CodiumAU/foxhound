<template>
  <PageSidebar v-bind="{ messages, route: { name: 'channels.mail' } }" />

  <PageContainer>
    <template v-if="uuid === undefined"> Select a message. </template>
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
import { useChannelsMailStore } from '../stores/channels-mail'
import PageSidebar from '../components/PageSidebar.vue'
import PageContainer from '../components/PageContainer.vue'
import { http } from '../http'
import { computed, watch } from 'vue'

const props = withDefaults(
  defineProps<{
    uuid?: string
  }>(),
  {
    uuid: undefined,
  }
)

const channelsMailStore = useChannelsMailStore()
const { messages } = storeToRefs(channelsMailStore)

await channelsMailStore.getMailMessages()

const iframeSource = computed(() => {
  if (props.uuid === undefined || props.uuid === '') {
    return null
  }

  return http.getUri({ url: `/channels/mail/${props.uuid}/html` })
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
