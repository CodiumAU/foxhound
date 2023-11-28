<template>
  <RouterLink v-slot="{ isActive, navigate, href }" custom v-bind="{ to }">
    <li class="grow-0">
      <a
        v-bind="{ href }"
        class="flex flex-col gap-y-1 py-2 px-2.5 text-xs rounded-lg focus:outline-none"
        :class="{
          'text-slate-700 dark:text-slate-300 bg-gray-100 dark:bg-gray-900/75':
            isActive,
          'text-gray-400 dark:text-slate-500 bg-gray-50 dark:bg-gray-900/25':
            read && !isActive,
          'text-slate-500 dark:text-slate-400 font-semibold hover:bg-gray-100 hover:dark:bg-gray-900/75':
            unread && !isActive,
        }"
        @click="navigate"
      >
        <div class="text-sm truncate" :class="{ 'font-semibold': isActive }">
          {{ message.subject }}
        </div>
        <div class="flex flex-row gap-2">
          <span class="truncate">
            to:
            <template
              v-for="recipient in message.recipients"
              :key="recipient.address"
            >
              &lt;{{ recipient.address }}&gt;
            </template>
          </span>
          <span class="ms-auto shrink-0">{{ sentAt }} ago</span>
        </div>
      </a>
    </li>
  </RouterLink>
</template>

<script lang="ts" setup>
import { computed } from 'vue'
import type { MessageListResource } from '../stores/channels-mail'
import { parseISO, intervalToDuration, formatDuration } from 'date-fns'
import type { RouteLocationNamedRaw } from 'vue-router'

const props = defineProps<{
  message: MessageListResource
  route: RouteLocationNamedRaw
}>()

const unread = computed(() => props.message.unread)
const read = computed(() => !unread.value)

const to = computed(() => ({
  ...props.route,
  params: { uuid: props.message.uuid },
}))

const sentAt = computed(() =>
  formatDuration(
    intervalToDuration({
      start: parseISO(props.message.sent_at),
      end: new Date(),
    }),
    {
      format: ['years', 'months', 'weeks', 'days', 'hours', 'minutes'],
    }
  )
)
</script>
