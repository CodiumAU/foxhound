<template>
  <AppSidebarItem v-if="icon" v-bind="{ to, icon }">
    {{ channel.name }}

    <span v-if="channel.unread_messages_count > 0" class="ms-auto py-1 px-2 rounded-full text-xs leading-none bg-red-500 text-white">
      {{ channel.unread_messages_count }}
    </span>
  </AppSidebarItem>
</template>

<script lang="ts" setup>
import { computed, toRefs } from 'vue'
import { ChannelType, type ChannelListResource } from '../../stores/channels'
import { EnvelopeIcon, DevicePhoneMobileIcon } from '@heroicons/vue/24/solid'
import AppSidebarItem from './AppSidebarItem.vue'

const props = defineProps<{
  channel: ChannelListResource
}>()

const { channel } = toRefs(props)

const icon = computed(() => {
  switch (channel.value.type) {
    case ChannelType.Mail:
      return EnvelopeIcon
    case ChannelType.Sms:
      return DevicePhoneMobileIcon
    default:
      return null
  }
})

const to = computed(() => ({
  name: 'channels.single',
  params: { channel: channel.value.key },
}))
</script>
