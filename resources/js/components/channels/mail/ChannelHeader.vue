<template>
  <h1 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
    {{ message.subject }}
  </h1>

  <h3 class="text-sm font-normal text-gray-700 dark:text-gray-200">
    {{ sentAt }}
  </h3>

  <ul class="flex flex-col my-3">
    <li
      class="inline-flex items-center gap-x-2 py-3 px-4 text-sm border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <div class="w-full grid grid-cols-12 gap-4 items-center">
        <span class="col-span-4 2xl:col-span-2 font-medium"> From </span>
        <span class="col-span-8 2xl:col-span-10">
          <RecipientBadge
            :name="message.data.from.name"
            :address="message.data.from.address"
            @click="search = `from:${message.data.from.address}`"
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
        <span class="col-span-4 2xl:col-span-2 font-medium">
          {{ label }}
        </span>
        <span class="col-span-8 2xl:col-span-10 flex flex-row gap-1 flex-wrap">
          <RecipientBadge
            v-for="{ name, address } in addresses"
            :key="`recipient-${address}`"
            :name="name"
            :address="address"
            class="cursor-pointer"
            @click="search = `${label.toLowerCase()}:${address}`"
          />
        </span>
      </div>
    </li>
    <li
      v-if="message.data.attachments.length > 0"
      class="inline-flex items-center gap-x-2 py-3 px-4 text-sm border text-gray-800 -mt-px first:rounded-t-lg first:mt-0 last:rounded-b-lg bg-white dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200"
    >
      <AttachmentBadge
        v-for="(attachment, index) in message.data.attachments"
        :key="`attachment-${index}`"
        v-bind="{ attachment }"
      />
    </li>
  </ul>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import {
  useChannelsStore,
  type MessageListResource,
} from '../../../stores/channels'
import { format, parseISO } from 'date-fns'
import { storeToRefs } from 'pinia'
import RecipientBadge from './RecipientBadge.vue'
import AttachmentBadge from './AttachmentBadge.vue'

const props = defineProps<{
  message: MessageListResource
}>()

const channelsStore = useChannelsStore()
const { search } = storeToRefs(channelsStore)

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

const sentAt = computed(() => {
  const date = parseISO(props.message.sent_at)

  return format(date, `EEEE, do MMMM yyyy 'at' h:mm a`)
})
</script>
