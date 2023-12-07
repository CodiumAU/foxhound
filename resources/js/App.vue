<template>
  <AppHeader />

  <Suspense>
    <AppSidebar />
  </Suspense>

  <div class="w-full h-full min-h-full xl:ps-[40rem] flex flex-1">
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

    // Close all overlays when the route changes.
    const overlays = document.querySelectorAll('[data-hs-overlay]')

    overlays.forEach((overlay) => {
      HSOverlay.close(overlay as HTMLElement)
    })
  },
  { immediate: true }
)

// Setup an interval to periodically fetch the channels to get unread messages count.
const channelsStore = useChannelsStore()
const interval = ref<number>()

onMounted(() => {
  if (interval.value) {
    clearInterval(interval.value)
  }

  interval.value = setInterval(async () => {
    await channelsStore.getChannels()
  }, 5000)
})

onBeforeUnmount(() => {
  clearInterval(interval.value)
})
</script>
