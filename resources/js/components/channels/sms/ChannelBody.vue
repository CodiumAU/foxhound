<template>
  <div
    class="grow rounded-lg rounded-t-none h-full w-full border border-t-0 dark:bg-gray-800 border-gray-200 dark:border-gray-700 bg-white overflow-hidden"
  >
    <ChannelBodyIframe
      v-if="uuid !== undefined && uuid !== ''"
      v-bind="{ uuid, channel: channel.key }"
    />
  </div>
</template>

<script lang="ts" setup>
import { type ChannelListResource } from '../../../stores/channels'
import ChannelBodyIframe from '../ChannelBodyIframe.vue'

defineProps<{
  uuid: string
  channel: ChannelListResource
}>()
</script>
