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
import { useRoute } from 'vue-router'
import AppHeader from './components/AppHeader.vue'
import AppSidebar from './components/AppSidebar.vue'
import { nextTick, watch } from 'vue'
import { HSDropdown, HSOverlay, HSTabs } from 'preline'

const route = useRoute()

watch(
  route,
  async () => {
    await nextTick()

    HSOverlay.autoInit()
    HSDropdown.autoInit()
    HSTabs.autoInit()
  },
  { immediate: true }
)
</script>
