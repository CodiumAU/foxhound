<template>
  <iframe :src="iframeSource" class="w-full h-full" />
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import { http } from '../http'

const props = defineProps<{
  uuid: string
  channel: string
}>()

const iframeSource = computed(() => {
  return http.getUri({
    url: `/channels/${props.channel}/messages/${props.uuid}`,
  })
})
</script>
