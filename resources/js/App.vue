<template>
  <AppHeader />

  <Suspense>
    <AppSidebar />
  </Suspense>

  <div class="w-full h-full min-h-full lg:ps-64 flex flex-1">
    <Suspense>
      <RouterView />
    </Suspense>
  </div>
</template>

<script lang="ts" setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useChannelsStore } from './stores/channels'
import { HSAccordion, HSDropdown, HSOverlay, HSTabs } from 'preline'
import { useRoute } from 'vue-router'
import AppHeader from './components/app/AppHeader.vue'
import AppSidebar from './components/app/AppSidebar.vue'

const route = useRoute()

watch(
  route,
  async () => {
    await nextTick()

    HSOverlay.autoInit()
    HSDropdown.autoInit()
    HSTabs.autoInit()
    HSAccordion.autoInit()
  },
  { immediate: true }
)

// Setup a timeout to periodically fetch the channels to get unread messages count.
const channelsStore = useChannelsStore()
const timeout = ref<number>()

onMounted(() => {
  if (timeout.value) {
    clearTimeout(timeout.value)
  }

  timeout.value = setTimeout(async () => {
    await channelsStore.getChannels()
  }, 5000)
})

onBeforeUnmount(() => {
  clearTimeout(timeout.value)
})
</script>
