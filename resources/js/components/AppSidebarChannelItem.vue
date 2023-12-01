<template>
  <AppSidebarItem v-if="icon" v-bind="{ to, icon }">{{
    channel.name
  }}</AppSidebarItem>
</template>

<script lang="ts" setup>
import { computed, toRefs } from 'vue'
import { ChannelType, type ChannelListResource } from '../stores/channels'
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
