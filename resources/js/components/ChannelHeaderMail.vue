<template>
  <ul class="flex flex-col my-3">
    <li
      class="inline-flex items-center gap-x-2 py-3 px-4 text-sm border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <div class="w-full grid grid-cols-12 gap-4 items-center">
        <span class="col-span-6 xl:col-span-4 2xl:col-span-2 font-medium">
          From
        </span>
        <span class="col-span-6 xl:col-span-8 2xl:col-span-10">
          <RecipientBadge
            :name="message.data.from.name"
            :address="message.data.from.address"
          />
        </span>
      </div>
    </li>
    <li
      v-for="{ label, addresses } in data"
      :key="label"
      class="inline-flex items-center gap-x-2 py-3 px-4 text-sm border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <div class="w-full grid grid-cols-12 gap-4 items-center">
        <span class="col-span-6 xl:col-span-4 2xl:col-span-2 font-medium">
          {{ label }}
        </span>
        <span
          class="col-span-6 xl:col-span-8 2xl:col-span-10 flex flex-row gap-1"
        >
          <RecipientBadge
            v-for="{ name, address } in addresses"
            :key="`recipient-${address}`"
            :name="name"
            :address="address"
          />
        </span>
      </div>
    </li>
  </ul>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import type { MessageListResource } from '../stores/channels'
import RecipientBadge from './RecipientBadge.vue'

const props = defineProps<{
  message: MessageListResource
}>()

const data = computed(() =>
  [
    {
      label: 'To',
      addresses: props.message.recipients,
    },
    {
      label: 'Reply To',
      addresses: props.message.data.replyTo,
    },
    {
      label: 'Cc',
      addresses: props.message.data.cc,
    },
    {
      label: 'Bcc',
      addresses: props.message.data.bcc,
    },
  ].filter(({ addresses }) => addresses.length > 0)
)
</script>
