<template>
  <li class="grow-0">
    <a
      class="flex flex-col gap-y-1 py-2 px-2.5 text-xs rounded-lg dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
      :class="{
        'text-slate-700 dark:text-slate-300 bg-gray-100 dark:bg-gray-900/75':
          active,
        'text-gray-400 dark:text-slate-600 bg-gray-50 dark:bg-gray-900/25':
          read && !active,
        'text-slate-700 dark:text-slate-300 font-semibold hover:bg-gray-100 hover:dark:bg-gray-900/75':
          unread && !active,
      }"
      href="#"
    >
      <div class="text-sm truncate" :class="{ 'font-semibold': active }">
        This is a test email with some really long text that should hide once it
        is too long
      </div>
      <div class="flex flex-row">
        <span>to: &lt;foo@bar.com&gt;</span>
        <span class="ms-auto">1 hour ago</span>
      </div>
    </a>
  </li>
</template>

<script lang="ts" setup>
import { computed, ref } from 'vue'
withDefaults(
  defineProps<{
    active?: boolean
  }>(),
  {
    active: false,
  }
)

const read = ref(false)
const unread = computed(() => !read.value)
</script>
