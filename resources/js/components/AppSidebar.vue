<template>
  <div
    id="application-sidebar"
    class="hs-overlay hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform hidden fixed top-0 start-0 bottom-0 z-[60] w-64 bg-gray-900 border-e border-gray-800 pt-4 pb-10 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500"
  >
    <div class="px-6">
      <a
        class="flex-none text-xl font-semibold text-slate-300 focus:outline-none focus:ring-1 focus:ring-gray-600"
        aria-label="Brand"
      >
        Interceptor
      </a>
    </div>

    <nav class="p-6 w-full flex flex-col flex-wrap">
      <ul class="space-y-1.5">
        <li class="text-base text-slate-300 pb-2 font-semibold">Channels</li>
        <AppSidebarItem :to="{ name: 'channels.mail' }" :icon="EnvelopeIcon">
          Mail
        </AppSidebarItem>
        <AppSidebarItem
          :to="{ name: 'channels.sms' }"
          :icon="DevicePhoneMobileIcon"
        >
          SMS
        </AppSidebarItem>
      </ul>
    </nav>
  </div>
</template>

<script lang="ts" setup>
import AppSidebarItem from './AppSidebarItem.vue'
import { EnvelopeIcon, DevicePhoneMobileIcon } from '@heroicons/vue/24/solid'
</script>
