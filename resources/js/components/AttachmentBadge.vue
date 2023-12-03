<template>
  <a
    class="shrink-0 inline-flex items-center leading-none gap-x-2 py-2 px-2.5 rounded-lg text-sm font-normal border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 dark:bg-slate-900 dark:hover:bg-slate-800 dark:border-gray-700 dark:text-white max-w-[14rem]"
    :href="attachment.url"
    :title="attachment.name"
    target="_blank"
  >
    <Component
      :is="iconComponent"
      class="shrink-0 w-5 h-5"
      :class="{
        'text-blue-500': attachment.type === 'image',
        'text-green-500': attachment.type === 'video',
        'text-red-500': attachment.type === 'document',
        'text-gray-500': attachment.type === 'other',
      }"
    />

    <span class="grow-0 flex flex-col gap-0.5 truncate">
      <span class="truncate">
        {{ attachment.name }}
      </span>

      <span class="text-gray-500 dark:text-slate-400 text-xs">
        {{ attachment.size }}
      </span>
    </span>
  </a>
</template>

<script lang="ts" setup>
import {
  PhotoIcon,
  VideoCameraIcon,
  DocumentTextIcon,
  DocumentIcon,
} from '@heroicons/vue/24/outline'
import { computed } from 'vue'

const props = defineProps<{
  attachment: {
    name: string
    size: string
    url: string
    type: 'image' | 'video' | 'document' | 'other'
  }
}>()

const iconComponent = computed(() => {
  switch (props.attachment.type) {
    case 'image':
      return PhotoIcon
    case 'video':
      return VideoCameraIcon
    case 'document':
      return DocumentTextIcon
    default:
      return DocumentIcon
  }
})
</script>
