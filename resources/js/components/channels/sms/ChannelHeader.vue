<template>
  <ul v-if="segmentedMessage" class="flex flex-col my-3">
    <li
      class="inline-flex items-center gap-x-2 pt-3 px-4 text-sm border border-b-0 text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <div class="font-medium mb-1">Segment breakdown</div>
    </li>
    <li
      class="max-h-32 xl:max-h-full box-content inline-flex items-center gap-x-2 py-3 px-4 text-sm border border-t-0 text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <div
        class="h-full w-full grid grid-cols-12 gap-4 items-center overflow-y-auto"
      >
        <span class="col-span-12 flex flex-wrap gap-0.5">
          <template v-for="(segments, number) in segmentedMessage.segments">
            <SmsSegmentBadge
              v-for="(segment, index) in segments"
              :key="index"
              v-bind="{ segment, number }"
            />
          </template>
        </span>
      </div>
    </li>
    <li
      v-if="segmentedMessage.segmentsCount > 1"
      class="py-3 px-4 text-sm border -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg text-yellow-800 bg-yellow-100 dark:bg-yellow-800/10 dark:border-yellow-900 dark:text-yellow-500"
    >
      This message will likely result in
      <strong>{{ segmentedMessage.segmentsCount }}</strong> segments when sent.
      This may result in additional charges.
    </li>
  </ul>
</template>

<script lang="ts" setup>
import { SegmentedMessage } from 'sms-segments-calculator'
import { shallowRef, watch } from 'vue'
import type { MessageListResource } from '../../../stores/channels'
import SmsSegmentBadge from './SmsSegmentBadge.vue'

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
