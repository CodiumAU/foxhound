<template>
  <ul v-if="segmentedMessage" class="flex flex-col my-3">
    <li
      class="inline-flex items-center gap-x-2 py-3 px-4 text-sm border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <div class="w-full grid grid-cols-12 gap-4 items-center">
        <span class="col-span-12 flex flex-wrap gap-0.5">
          <div class="basis-full font-medium mb-1">Segment breakdown</div>

          <template v-for="(segments, number) in segmentedMessage.segments">
            <SmsSegmentBadge
              v-for="(segment, index) in segments"
              :key="index"
              v-bind="{ segment, number }"
            />
          </template>
        </span>

        <div
          v-if="segmentedMessage.segmentsCount > 1"
          class="col-span-12 bg-yellow-100 border-y border-yellow-200 text-sm text-yellow-800 -mx-4 px-4 py-3 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500"
        >
          This message will likely result in
          <strong>{{ segmentedMessage.segmentsCount }}</strong> segments when
          sent. This may result in additional charges.
        </div>
      </div>
    </li>
  </ul>
</template>

<script lang="ts" setup>
import { SegmentedMessage } from 'sms-segments-calculator'
import type { MessageListResource } from '../stores/channels'
import SmsSegmentBadge from './SmsSegmentBadge.vue'
import { shallowRef, watch } from 'vue'

const props = defineProps<{
  message: MessageListResource
}>()

const segmentedMessage = shallowRef<SegmentedMessage | null>(null)

watch(
  () => props.message,
  () => {
    segmentedMessage.value = new SegmentedMessage(props.message.subject)
  },
  { immediate: true }
)
</script>
