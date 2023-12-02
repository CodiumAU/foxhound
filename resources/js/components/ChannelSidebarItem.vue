<template>
  <RouterLink
    v-slot="{ isActive, navigate, href }"
    custom
    :to="{
      name: 'channels.single',
      params: { channel, uuid: props.message.uuid },
    }"
  >
    <li class="grow-0">
      <a
        v-bind="{ href }"
        class="flex flex-col gap-y-1 py-2 px-2.5 text-xs rounded-lg focus:outline-none"
        :class="{
          'text-slate-700 dark:text-slate-300 bg-gray-100 dark:bg-gray-900/75':
            isActive,
          'text-gray-400 dark:text-slate-500 bg-gray-50 dark:bg-gray-900/25':
            read && !isActive,
          'text-slate-600 dark:text-slate-400 font-semibold hover:bg-gray-100 hover:dark:bg-gray-900/75':
            unread && !isActive,
        }"
        @click="navigate"
      >
        <div class="text-sm flex flex-row gap-2">
          <span class="truncate" :class="{ 'font-semibold': isActive }">
            {{ message.subject }}
          </span>

          <PaperClipIcon
            v-if="message.has_attachments"
            class="ms-auto shrink-0 w-4 h-4"
          />
        </div>
        <div class="flex flex-row gap-3">
          <div class="flex flex-row truncate gap-3">
            <span class="truncate">{{ recipient.address }} </span>
            <span v-if="additionalRecipients > 0" class="shrink-0">
              (+{{ additionalRecipients }})
            </span>
          </div>
          <span class="ms-auto shrink-0">{{ sentAt }}</span>
        </div>
      </a>
    </li>
  </RouterLink>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref } from 'vue'
import type { MessageListResource } from '../stores/channels'
import { parseISO, formatDistanceToNowStrict } from 'date-fns'
import { PaperClipIcon, PlusIcon } from '@heroicons/vue/20/solid'

const props = defineProps<{
  message: MessageListResource
  channel: string
}>()

const recipient = computed(() => props.message.recipients[0])
const additionalRecipients = computed(
  () => props.message.recipients.slice(1).length
)
const unread = computed(() => props.message.unread)
const read = computed(() => !unread.value)
const sentAt = ref(parseSentAt())

function parseSentAt() {
  return formatDistanceToNowStrict(parseISO(props.message.sent_at), {
    addSuffix: true,
  })
}

onMounted(() => {
  setInterval(() => {
    sentAt.value = parseSentAt()
  }, 1000 * 60)
})
</script>
