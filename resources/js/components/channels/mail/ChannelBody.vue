<template>
  <select
    id="tab-select"
    class="sm:hidden py-3 px-4 pe-9 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400"
    aria-label="Tabs"
    role="tablist"
  >
    <option value="#preview">Tab 1</option>
    <option value="#html-check">Tab 2</option>
    <option value="#hs-tab-to-select-3">Tab 3</option>
  </select>

  <div class="hidden sm:block border-b border-gray-200 dark:border-gray-700">
    <nav
      class="flex space-x-2"
      aria-label="Tabs"
      role="tablist"
      hs-data-tab-select="#tab-select"
    >
      <button
        type="button"
        class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 dark:hs-tab-active:bg-gray-800 dark:hs-tab-active:border-b-gray-800 dark:hs-tab-active:text-white -mb-px py-3 px-4 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
        :class="{ active: tab === 'preview' }"
        data-hs-tab="#preview"
        aria-controls="preview"
        role="tab"
        @click="tab = 'preview'"
      >
        Preview
      </button>
      <button
        type="button"
        class="hs-tab-active:bg-white hs-tab-active:border-b-transparent hs-tab-active:text-blue-600 dark:hs-tab-active:bg-gray-800 dark:hs-tab-active:border-b-gray-800 dark:hs-tab-active:text-white -mb-px py-3 px-4 inline-flex items-center gap-2 bg-gray-50 text-sm font-medium text-center border text-gray-500 rounded-t-lg hover:text-gray-700 disabled:opacity-50 disabled:pointer-events-none dark:bg-gray-700 dark:border-gray-700 dark:text-gray-400 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
        :class="{ active: tab === 'html-check' }"
        data-hs-tab="#html-check"
        aria-controls="html-check"
        role="tab"
        @click="tab = 'preview'"
      >
        HTML Check

        <span
          class="w-2 h-2 inline-block rounded-full"
          :class="{
            'bg-red-500': result === false,
            'bg-green-500': result === true,
            'bg-gray-500': result === null,
          }"
        ></span>
      </button>
    </nav>
  </div>

  <div
    class="grow rounded-lg rounded-t-none h-full w-full border border-t-0 dark:bg-gray-800 border-gray-200 dark:border-gray-700 bg-white overflow-hidden"
  >
    <ChannelBodyIframe
      v-if="uuid !== undefined && uuid !== ''"
      v-bind="{ uuid, channel: channel.key }"
      id="preview"
    />

    <ChannelBodyHtmlCheck
      id="html-check"
      class="hidden"
      v-bind="{ uuid }"
      @result="updateHtmlCheckResult"
    />
  </div>
</template>

<script lang="ts" setup>
import { nextTick, ref, watch } from 'vue'
import { type ChannelListResource } from '../../../stores/channels'
import ChannelBodyIframe from '../ChannelBodyIframe.vue'
import ChannelBodyHtmlCheck from './ChannelBodyHtmlCheck.vue'
import { HSTabs } from 'preline'

const props = defineProps<{
  uuid: string
  channel: ChannelListResource
}>()

const tab = ref<'preview' | 'html-check'>('preview')
const result = ref<boolean | null>(null)
function updateHtmlCheckResult(success: boolean) {
  result.value = success
}

watch(
  () => props.uuid,
  async () => {
    tab.value = 'preview'
    result.value = null

    await nextTick()

    HSTabs.open(document.querySelector('[data-hs-tab="#preview"]')!)
  }
)
</script>
