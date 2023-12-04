import { createRouter, createWebHistory } from 'vue-router'
import ChannelsSingle from './pages/ChannelsSingle.vue'
import SettingsStorageCleanup from './pages/SettingsStorageCleanup.vue'

export const router = createRouter({
  history: createWebHistory('foxhound'),
  routes: [
    { path: '/', redirect: '/channels/mail' },
    {
      path: '/channels/:channel/:uuid?',
      name: 'channels.single',
      props: true,
      component: ChannelsSingle,
    },
    {
      path: '/settings/storage-cleanup',
      name: 'settings.storage-cleanup',
      component: SettingsStorageCleanup,
    },
  ],
})
