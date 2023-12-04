<template>
  <span
    v-if="segment.isUserDataHeader"
    class="box-content inline-flex items-center justify-center gap-x-1.5 py-0.5 px-1.5 rounded text-xs font-normal bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-white font-mono w-[6ch]"
  >
    <Squares2X2Icon class="w-3 h-3" />
  </span>
  <span
    v-for="(codeUnit, index) in segment.codeUnits"
    v-else
    :key="index"
    class="box-content inline-flex items-center justify-center gap-x-1.5 py-0.5 px-1.5 rounded text-xs font-normal font-mono w-[6ch]"
    :class="{
      'bg-blue-100 text-blue-800 dark:bg-blue-800/30 dark:text-blue-50':
        number % 2 === 1 && segment.isGSM7,
      'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-white':
        number % 2 === 0 && segment.isGSM7,
      'bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-50':
        segment.isGSM7 === false,
    }"
  >
    {{ `0x${codeUnit.toString(16).padStart(4, '0').toUpperCase()}` }}
  </span>
</template>

<script lang="ts" setup>
import type { SegmentedMessage } from 'sms-segments-calculator'
import { Squares2X2Icon } from '@heroicons/vue/20/solid'

defineProps<{
  segment: SegmentedMessage['segments'][0][0]
  number: number
}>()
</script>
