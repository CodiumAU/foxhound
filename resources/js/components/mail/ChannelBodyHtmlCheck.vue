<template>
  <div>
    <div
      v-if="result && result.success"
      class="bg-green-100 border-b border-green-200 text-sm text-green-800 p-4 dark:bg-green-800/10 dark:border-green-900 dark:text-green-500"
      role="alert"
    >
      This message is compatible with all tested email clients. There may be
      some notes below you can use to improve the message.
    </div>
    <div
      v-else-if="result && !result.success"
      class="bg-red-100 border-b border-red-200 text-sm text-red-800 p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500"
      role="alert"
    >
      This message is not compatible with all tested email clients.
    </div>
    <div class="hs-accordion-group">
      <div
        v-for="([client, results], index) in resultsGrouped"
        :id="`html-check-heading-${client}`"
        :key="client"
        class="hs-accordion dark:border-gray-700"
        :class="{
          'border-y': index !== 0,
          'border-none': index === resultsGrouped.length - 1,
        }"
      >
        <button
          class="hs-accordion-toggle hs-accordion-active:text-blue-600 inline-flex items-center gap-x-3 w-full font-semibold text-start text-gray-800 py-4 px-5 hover:text-gray-500 disabled:opacity-50 disabled:pointer-events-none dark:hs-accordion-active:text-blue-500 dark:text-gray-200 dark:hover:text-gray-400 dark:focus:outline-none dark:focus:text-gray-400"
          :aria-controls="`html-check-content-${client}`"
        >
          <PlusIcon class="hs-accordion-active:hidden block w-4 h-4" />
          <MinusIcon class="hs-accordion-active:block hidden w-4 h-4" />

          {{ client }}
        </button>

        <div
          :id="`html-check-content-${client}`"
          class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300 hidden"
          :aria-labelledby="`html-check-heading-${client}`"
        >
          <div class="pb-4 px-5">
            <span v-if="results.length === 0" class="text-gray-500 text-sm">
              There are no messages for this client.
            </span>

            <ul class="flex flex-col gap-y-1">
              <!-- eslint-disable vue/no-v-html -->
              <li
                v-for="({ type, message }, resultIndex) in results"
                :key="`${client}-${resultIndex}`"
                class="py-3 px-2 text-sm font-normal text-gray-800"
                :class="{
                  'bg-red-100': type === 'error',
                  'bg-yellow-100': type === 'warning',
                  'bg-blue-100': type === 'note',
                }"
                v-html="message"
              />
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { doIUseEmail } from '@jsx-email/doiuse-email'
import { http } from '../../http'
import { computed, ref, watch } from 'vue'
import { MinusIcon, PlusIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  uuid: string
}>()

const emit = defineEmits<{
  result: [value: boolean]
}>()

const result = ref<ReturnType<typeof doIUseEmail> | null>(null)
const resultsGrouped = computed(() => {
  const results = {
    gmail: [],
    outlook: [],
    'apple-mail': [],
  } as Record<string, { type: 'note' | 'error' | 'warning'; message: string }[]>

  if (result.value?.success === false) {
    result.value.errors.forEach((error) => {
      const { client, message } = prepareMessage(error)

      if (!client) {
        return
      }

      results[client].push({ type: 'error', message })
    })
  }
  result.value?.notes.forEach((note) => {
    const { client, message } = prepareMessage(note)

    if (!client) {
      return
    }

    results[client].push({ type: 'note', message })
  })

  result.value?.warnings.forEach((warning) => {
    const { client, message } = prepareMessage(warning)

    if (!client) {
      return
    }

    results[client].push({ type: 'warning', message })
  })

  return Object.entries(results)
})

function prepareMessage(message: string) {
  const client = message.match(/(gmail|outlook|apple-mail)\..*?/)

  return {
    client: client === null ? null : client[1],
    message: message.replace(
      /`(.*?)`/g,
      '<pre class="inline font-mono">$1</pre>'
    ),
  }
}

watch(
  () => props.uuid,
  async () => {
    const response = await http.get(`/channels/mail/messages/${props.uuid}`)

    if (response.status === 200) {
      result.value = doIUseEmail(response.data, {
        emailClients: ['gmail.*', 'outlook.*', 'apple-mail.*'],
      })

      emit('result', result.value.success)
    }
  },
  { immediate: true }
)
</script>
