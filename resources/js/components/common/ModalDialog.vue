<template>
  <button v-bind="hsDataAttributes" ref="element" class="hidden" />
  <div
    v-bind="{ id }"
    class="hs-overlay hidden w-full h-full fixed top-0 start-0 z-[100] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]"
    data-hs-overlay-keyboard="false"
  >
    <div
      class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center"
    >
      <div
        class="w-full flex flex-col bg-white border shadow-sm rounded-xl dark:bg-gray-800 dark:border-gray-700 dark:shadow-slate-700/[.7]"
      >
        <div class="py-4 px-4 text-center">
          <h2 class="font-semibold text-xl text-gray-800 dark:text-white">
            {{ title }}
          </h2>
        </div>
        <div class="px-4 py-2 overflow-y-auto">
          <p class="mt-1 text-gray-700 dark:text-gray-400 text-center">
            {{ message }}
          </p>
        </div>
        <div class="flex justify-end items-center gap-x-2 py-4 px-4">
          <button
            v-if="confirmationOnly === false"
            type="button"
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-gray-200 bg-white text-gray-800 shadow-sm hover:bg-gray-50 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-white dark:hover:bg-gray-800 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
            @click="cancel"
          >
            {{ cancelButton }}
          </button>

          <button
            class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-white disabled:opacity-50 disabled:pointer-events-none dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
            :class="{
              'bg-blue-600 hover:bg-blue-700': !danger,
              'bg-red-500 hover:bg-red-600': danger,
            }"
            @click="confirm"
          >
            {{ confirmButton }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { HSOverlay } from 'preline'

export type Props = {
  title: string
  message: string
  danger?: boolean
  cancelButton?: string
  confirmButton?: string
  confirmationOnly?: boolean
  onResponse: (confirmed: boolean) => Promise<void>
}

const props = withDefaults(defineProps<Props>(), {
  danger: false,
  cancelButton: 'Cancel',
  confirmButton: 'Ok',
  confirmationOnly: false,
})
const emit = defineEmits<{
  opened: []
  closed: []
}>()

const id = btoa(Math.random().toString()).substring(0, 12)
const element = ref<HTMLElement>()
const overlay = ref<HSOverlay>()
const hsDataAttributes = computed(() => ({
  'data-hs-overlay': `#${id}`,
}))

async function confirm() {
  await close()
  await props.onResponse(true)
}

async function cancel() {
  await close()
  await props.onResponse(false)
}

async function close() {
  if (!overlay.value) {
    return
  }

  await overlay.value.close()
}

onMounted(async () => {
  if (!element.value) {
    return
  }

  overlay.value = new HSOverlay(element.value)
  await overlay.value.open()

  overlay.value.on('close', async () => {
    emit('closed')
  })

  emit('opened')
})
</script>
